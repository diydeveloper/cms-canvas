<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Text_field extends Field_type
{
    function view($data)
    {
        return $this->load->view('text', $data, TRUE);
    }
}
