<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Radio_field extends Field_type
{
    function settings()
    {
        $data = get_object_vars($this);
        
        return $this->load->view('settings/radio', $data, TRUE);
    }

    function display_field()
    {
        $data = get_object_vars($this);
        
        return $this->load->view('radio', $data, TRUE);
    }
}
