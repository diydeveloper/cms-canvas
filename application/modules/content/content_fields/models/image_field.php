<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Image_field extends Field_type
{
    function settings()
    {
        $data = get_object_vars($this);
        
        return $this->load->view('settings/image', $data, TRUE);
    }

    function save()
    {
        if (is_null($this->content['src']) && is_null($this->content['alt']))
        {
            return NULL;
        }

        // Convert the content array to a serialized string
        return serialize($this->content);
    }

    function display_field()
    {
        $data = get_object_vars($this);
        
        $this->template->add_javascript('/application/modules/content/content_fields/assets/js/image.js');
        return $this->load->view('image', $data, TRUE);
    }

    function output($attributes = array())
    {
        $settings = $this->Field->settings;

        // Image thumbnail size override
        if ( ! empty($attributes['width']) || ! empty($attributes['height']))
        {
            $max_width = ( ! empty($attributes['width'])) ? $attributes['width'] : 0;
            $max_height = ( ! empty($attributes['height'])) ? $attributes['height'] : 0;
            $crop = (isset($attributes['crop']) && str_to_bool($attributes['crop'])) ? TRUE : FALSE;
            $image_src = image_thumb($this->content['src'], $max_width, $max_height, $crop);
        }
        // Image settings thumbnail size
        else if ( ! empty($settings['max_width']) || ! empty($settings['max_height']))
        {
            $max_width = ( ! empty($settings['max_width'])) ? $settings['max_width'] : 0;
            $max_height = ( ! empty($settings['max_height'])) ? $settings['max_height'] : 0;
            $crop = (isset($settings['crop']) && $settings['crop'] == '1') ? TRUE : FALSE;
            $image_src = image_thumb($this->content['src'], $max_width, $max_height, $crop);
        }
        else
        {
            $image_src = base_url($this->content['src']);
        }

        // Show output with image tags
        if ($settings['output'] == 'image')
        {
            // Inline editing callback override
            if (isset($attributes['editable'])) 
            {
                if (str_to_bool($attributes['editable']))
                {
                    return $this->_inline_editable($image_src, TRUE);
                }
                else
                {
                    return $this->_inline_editable($image_src, FALSE);
                }
            }
            // Inline editing defined by settings
            else
            {
                if ( ! isset($settings['inline_editing']) || $settings['inline_editing'])
                {
                    return $this->_inline_editable($image_src, TRUE);
                }
                else
                {
                    return $this->_inline_editable($image_src, FALSE);
                }
            }
        }
        // Only return the image URL
        else
        {
            return $image_src;
        }
    }

    function validate()
    {
        $this->CI->form_validation->set_rules('field_id_' . $this->Field->id . '[src]', $this->Field->label, 'trim' . (($this->Field->required) ? '|required' : ''));
        $this->CI->form_validation->set_rules('field_id_' . $this->Field->id . '[alt]', $this->Field->label . 'Alternative Text', 'trim');

        return TRUE;
    }

    function set_content($content)
    {
        // Initalize the structure for the content array
        $this->content = array('src' => null, 'alt' => null);

        if (is_array($content))
        {
            // An array of content was passed. Most likely from an entry edit form post.
            $this->content['src'] = (isset($content['src'])) ? $content['src'] : null;
            $this->content['alt'] = (isset($content['alt'])) ? $content['alt'] : null;
        }
        else if ($content != '')
        {
            // A serialized string was passed. Most likely from the DB.
            $unserialized_content = @unserialize($content);

            if ($unserialized_content !== false)
            {
                $this->content['src'] = (isset($unserialized_content['src'])) ? $unserialized_content['src'] : null;
                $this->content['alt'] = (isset($unserialized_content['alt'])) ? $unserialized_content['alt'] : null;
            }
            else
            {
                // If the string wasn't unserializeable then we will just assume it was the src
                $this->content['src'] = $content;
            }
        }
    }

    function parser_callback($tag, $attributes, $content, $data)
    {
        return $this->output($attributes);
    }

    private function _inline_editable($image_src, $editable)
    {
        $settings = $this->Field->settings;

        $id = ($settings['id']) ? ' id="' . $settings['id'] . '"' : '';
        $alt = ($this->content['alt']) ? ' alt="' . $this->content['alt'] . '"' : '';

        if ($this->is_inline_editable() && $editable)
        {
            $this->template->add_javascript('/application/modules/content/content_fields/assets/js/image_inline_editable.js');
            $_SESSION['KCFINDER'] = array();
            $_SESSION['KCFINDER']['disabled'] = false;
            $_SESSION['isLoggedIn'] = true;

            $class = ($settings['class']) ? ' class="' . $settings['class'] . ' cc_image_editable' . '"' : ' class="cc_image_editable"';

            return '<img' . $id . $class . $alt . ' src="' . $image_src . '" /><input id="cc_field_' . $this->Entry->id . '_'. $this->Field->id  . '" class="cc_hidden_editable" type="hidden" value="' . $this->content['src'] . '" />';
        }
        else
        {
            $class = ($settings['class']) ? ' class="' . $settings['class'] . '"' : '';
            return '<img' . $id . $class . $alt . ' src="' . $image_src . '" />';
        }
    }
}
