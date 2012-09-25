<?php

class Gallery_images_model extends DataMapper
{	
    public $table = "gallery_images";

    public $has_one = array(
        'gallery' => array(
            'class' => 'galleries_model',
            'other_field' => 'images',
            'join_self_as' => 'image',
            'join_other_as' => 'gallery',
        ),
    );

/* Used to Sync Directory with database (No Longer in Use)

    function directory_images($directory)
    {
        $this->load->helper('file');
        $dir = get_dir_file_info(FCPATH . ltrim($directory, '/'));

        if ( ! is_array($dir))
        {
            $dir = (array) $dir;
        }

        $images = array();

        foreach($dir as $filename => $file_info)
        {
            $info = pathinfo($file_info['name']);

            if (isset($info['extension']) && in_array($info['extension'], array('jpg', 'JPG', 'gif', 'GIF', 'png', 'PNG')))
            {
                $images[] = $file_info['name'];
            }
        }

        return $images;
    }

    function database_images($gallery_id)
    {
        $Gallery_images = new Gallery_images_model();
        $Gallery_images->get_by_gallery_id($gallery_id);

        $images = array();

        foreach($Gallery_images as $DB_image)
        {
            $images[$DB_image->id] = $DB_image->filename;
        }

        return $images;
    }
*/
}
