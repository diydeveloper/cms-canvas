<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Image_field extends Field_type
{
    function view($data)
    {
        $this->template->add_javascript('/application/modules/content/content_fields/assets/js/image.js');
        return $this->load->view('image', $data, TRUE);
    }
}
