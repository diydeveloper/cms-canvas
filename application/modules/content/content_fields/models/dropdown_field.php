<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Dropdown_field extends Field_type
{
    function settings()
    {
        $data = get_object_vars($this);
        
        return $this->load->view('settings/dropdown', $data, TRUE);
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
        
        return $this->load->view('dropdown', $data, TRUE);
    }
}
