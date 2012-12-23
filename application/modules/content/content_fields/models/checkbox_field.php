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
        // Convert data array to pipe delimited string
        if ($this->content != '')
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

    function output()
    {
        $value_array = array();

        if ($this->content != '')
        {
            $this->parser->set_callback($this->Field->short_tag, array($this, 'checkbox_callback'));

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
    }

    function checkbox_callback($trigger, $parameters, $content, $data)
    {
        if ( ! isset($data[$trigger]))
        {
            return '';
        }

        $values = $data[$trigger];

        // Check if the last element needs to be trimmed
        if (is_array($values) && isset($parameters['backspace']))
        {
            $values[count($values) - 1]['_content'] = substr($content, 0, $parameters['backspace'] * -1);
        } 

        return $values;
    }
}
