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
        
        return $this->load->view('dropdown', $data, TRUE);
    }
}
