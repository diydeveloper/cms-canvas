<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Html_field extends Field_type
{
    function display_field()
    {
        $data = get_object_vars($this);
        
        $this->template->add_package('codemirror');

        return $this->load->view('html', $data, TRUE);
    }
}
