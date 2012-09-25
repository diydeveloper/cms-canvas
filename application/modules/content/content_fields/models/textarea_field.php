<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Textarea_field extends Field_type
{
    function settings($data)
    {
        return $this->load->view('settings/textarea', $data, TRUE);
    }

    function view($data)
    {
        return $this->load->view('textarea', $data, TRUE);
    }
}
