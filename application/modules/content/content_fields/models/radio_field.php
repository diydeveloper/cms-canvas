<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Radio_field extends Field_type
{
    function settings($data)
    {
        return $this->load->view('settings/radio', $data, TRUE);
    }

    function view($data)
    {
        return $this->load->view('radio', $data, TRUE);
    }
}
