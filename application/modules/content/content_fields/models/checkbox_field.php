<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Checkbox_field extends Field_type
{
    function settings()
    {
        $data = get_object_vars($this);

        return $this->load->view('settings/checkbox', $data, TRUE);
    }

    function save()
    {
        // If the hidden checkbox indicator field is posted but the field is not, this means that no checkboxes were selected
        if ($this->input->post('field_id_' . $this->Field->id . '_checkbox') !== FALSE && $this->input->post('field_id_' . $this->Field->id) === FALSE)
        {
            return NULL;
        }

        // Convert data array to pipe delimited string
        if ( ! empty($this->content))
        {
            return implode('|', $this->content);
        }

        return NULL;
    }

    function display_field()
    {
        $data = get_object_vars($this);

        // Build options array
        $option_array = array();
        foreach (explode("\n", $this->Field->options) as $option)
        {
            $option = explode("=", $option, 2);
            $option_array[$option[0]] = (count($option) == 2) ? $option[1] : $option[0];
        }

        $data['Field']->options = $option_array;

        return $this->load->view('checkbox', $data, TRUE);
    }

    function validate()
    {
        $this->CI->form_validation->set_rules('field_id_' . $this->Field->id . '[]', $this->Field->label, 'trim' . (($this->Field->required) ? '|required' : ''));

        return TRUE;
    }

    function output()
    {
        $value_array = array();

        if ( ! empty($this->content))
        {
            foreach($this->content as $value)
            {
                $value_array[] = array('item' => $value);
            }

            return $value_array;
        }

        return '';
    }

    function set_content($content)
    {
        if (is_array($content))
        {
            $this->content = $content;
        }
        else if ($content != '')
        {
            $this->content = explode('|', $content);
        }
        else
        {
            $this->content = array();
        }
    }

    function parser_callback($tag, $attributes, $content, $data)
    {
        if ( ! isset($data[$tag]))
        {
            return '';
        }

        $values = $data[$tag];

        // Check if the last element needs to be trimmed
        if (is_array($values) && isset($attributes['backspace']))
        {
            $values[count($values) - 1]['_content'] = substr($content, 0, $attributes['backspace'] * -1);
        } 

        return $values;
    }
}
