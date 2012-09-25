<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Dropdown_field extends Field_type
{
    function settings($data)
    {
        return $this->load->view('settings/dropdown', $data, TRUE);
    }

    function view($data)
    {
        return $this->load->view('dropdown', $data, TRUE);
    }
}
