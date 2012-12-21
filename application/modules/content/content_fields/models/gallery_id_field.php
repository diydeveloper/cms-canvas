<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Gallery_id_field extends Field_type
{
    function display_field()
    {
        $data = get_object_vars($this);
        
        // Get all entries for link dropdown
        $this->load->model('galleries/galleries_model');

        $Galleries = new Galleries_model();
        $Galleries->order_by('title')->get();

        $gallery_array = array('' => '');

        foreach($Galleries as $Gallery)
        {
            $gallery_array[$Gallery->id] = $Gallery->title;
        }

        $data['Galleries'] = $gallery_array;

        return $this->load->view('galleries', $data, TRUE);
    }
}
