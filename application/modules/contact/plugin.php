<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

/**
 * Contact Plugin
 *
 * Build and send contact forms
 *
 */
class Contact_plugin extends Plugin
{
    private $contact_form = array(
        'fields' => array(
            'name' => array(
                'label' => 'Name',
                'type'  => 'text',
                'validation'  => 'trim|required',
            ),
            'email' => array(
                'label' => 'Email',
                'type'  => 'text',
                'validation'  => 'trim|required|valid_email',
            ),
            'phone' => array(
                'label' => 'Phone',
                'type'  => 'text',
                'validation'  => 'trim|required',
            ),
            'message' => array(
                'label' => 'Message',
                'type'  => 'textarea',
                'validation'  => 'trim|required',
            ),
            '' => array(
                'label' => '',
                'type' => 'submit',
                'value' => 'Send',
            ),
        ),

    );

    /*
     * Form
     *
     * Outputs and sets form validations. 
     * If no formatting content specified, the default form will be used
     *
     * @access private
     * @return void
     */
    public function form()
    {
        $content = $this->content();

        if ($content != '')
        {
            // Wrap content with form tags and add a spam check field
            // A theory that spam bots do not read css and will attempt to fill all fields
            // The form will not submit if the hidden field has been filled
            $content = '<form' . (($this->attribute('anchor')) ? ' action="' . current_url() . $this->attribute('anchor') . '"' : '')  . ' method="post"' . ($this->attribute('id') ? ' id="' . $this->attribute('id') . '"' : '') . ($this->attribute('class') ? ' class="' . $this->attribute('class') . '"' : '') . '>' . $content . '<div style="display: none;"><input type="text" name="spam_check" value="" />' . (($this->attribute('id')) ? '<input type="hidden" name="form_id" value="' . $this->attribute('id') . '" />' : '') . '</div></form>';

            if ($this->attribute('id') == '' || $this->attribute('id') == $this->input->post('form_id'))
            {
                // Repopulate form by default
                if ($this->input->post() && $this->attribute('required'))
                {
                    $content = $this->_repopulate_form($content);
                }

                // Set required fields
                if ($required = $this->attribute('required'))
                {
                    foreach(explode('|', $required) as $name)
                    {
                        $this->form_validation->set_rules($name, $name, 'required');
                    }
                }

                // Process Form
                if ($this->form_validation->run() == TRUE && $this->input->post('spam_check') == '')
                {
                    $this->_send_form();
                    return "Your message has been sent. Thank You";
                }

                // Add validation errors to the content
                $content = validation_errors() . $content;
            }

            return array('_content' => $content);
        }
        else
        {
            // No custom content was set, use the default form
            $data['id'] = $this->attribute('id');
            $data['class'] = $this->attribute('class');
            $data['anchor'] = $this->attribute('anchor');
            $data['Form'] = $this->load->library('formation', $this->contact_form);
            $data['Form']->populate();

            if ($this->attribute('id') == '' || $this->attribute('id') == $this->input->post('form_id'))
            {
                if ($data['Form']->validate() == TRUE && $this->input->post('spam_check') == '')
                {
                    $this->_send_form();
                    return "Your message has been sent. Thank You";
                }
            }

            // Load view
            return $this->load->view('contact', $data, TRUE);
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Send Form
     *
     * Builds and sends email to the specified address
     *
     * @access private
     * @return void
     */
    private function _send_form()
    {
        $this->load->library('email');
        $this->email->from($this->attribute('from', 'noreply@' . domain_name()), $this->attribute('from_name', $this->settings->site_name));
        $this->email->to($this->attribute('to', $this->settings->notification_email)); 
        $this->email->subject($this->attribute('subject', 'Contact Form Submission'));

        // Remove Spam Check
        unset($_POST['spam_check']);
        unset($_POST['form_id']);

        // Build message from $_POST array
        $message = '';
        foreach($_POST as $field => $value)
        {
            if (is_array($value))
            {
                $message .= ucwords(str_replace('_', ' ', $field)) . ' : ' . "\r\n";

                foreach ($value as $arr_val)
                {
                    $message .= "\t" . $arr_val . "\r\n";
                }
            }
            else
            {
                $message .= ucwords(str_replace('_', ' ', $field)) . ' : ' . $value . "\r\n";
            }
        }

        $this->email->message($message);  
        $this->email->send();
    }

    // ------------------------------------------------------------------------

    /*
     * Repopulate Form
     *
     * Repopulates a custom formatted form
     *
     * @access private
     * @return string
     */
    private function _repopulate_form($content)
    {
        $DOM = new DOMDocument;
        @$DOM->loadHTML($content);
        $Xpath = new DOMXPath($DOM);

        // Remove <!DOCTYPE 
        $DOM->removeChild($DOM->firstChild);            

        // Remove <html><body></body></html> 
        $DOM->replaceChild($DOM->firstChild->firstChild->firstChild, $DOM->firstChild);

        // Repopulate Text and Password Inputs
        $inputs = $Xpath->query('//input[@type="text"] | //input[@type="password"]');
        foreach ($inputs as $Input) 
        {
            if ($name = $Input->getAttribute('name')) 
            {
                $Input->setAttribute('value', $this->input->post($name));
            }
        }

        // Repopulate Radio and Checkbox Inputs
        $inputs = $Xpath->query('//input[@type="radio"] | //input[@type="checkbox"]');
        foreach ($inputs as $Input) 
        {
            if ($name = $Input->getAttribute('name')) 
            {
                $value = $Input->getAttribute('value');
                if ($this->input->post($name) == $value)
                {
                    $Input->setAttribute('checked', 'checked');
                }
            }
        }

        // Repopulate Textareas
        $textareas = $Xpath->query('//textarea');
        foreach ($textareas as $Textarea) 
        {
            if ($name = $Textarea->getAttribute('name')) 
            {
                $Textarea->nodeValue = $this->input->post($name);
            }
        }

        // Repopulate Dropdowns
        $options = $Xpath->query('//select/option');
        foreach ($options as $Option) 
        {
            if ($name = $Option->parentNode->getAttribute('name')) 
            {
                $value = $Option->getAttribute('value');
                if ($this->input->post($name) == $value)
                {
                    $Option->setAttribute('selected', 'selected');
                }
            }
        }

        return $DOM->saveHTML();
    }
}
