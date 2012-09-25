<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class File_field extends Field_type
{
    function view($data)
    {
        $this->template->add_javascript('/application/modules/content/content_fields/assets/js/file.js');
        return $this->load->view('file', $data, TRUE);
    }
}
