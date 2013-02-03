<?php
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Galleries_model extends DataMapper
{	
    public $table = "galleries";

    public $has_many = array(
        'images' => array(
            'class' => 'gallery_images_model',
            'other_field' => 'gallery',
            'join_self_as' => 'gallery',
            'join_other_as' => 'image',
        ),
    );

    function sync_db()
    {
        $CI =& get_instance();
        $CI->load->model('gallery_images_model');
        $CI->load->helper('file');

        $dir_images = $CI->gallery_images_model->directory_images($this->directory);
        $db_images = $CI->gallery_images_model->database_images($this->id);

        $delete_records = array_diff($db_images, $dir_images);
        $insert_records = array_diff($dir_images, $db_images);

        foreach($delete_records as $id => $filename)
        {
            // Delete image thumbs
            $info = pathinfo($filename);
            delete_files(CMS_ROOT . ltrim($this->directory, '/') . '/thumbs/' . $info['filename'] . '_' . $info['extension'], TRUE);
            @rmdir(CMS_ROOT . ltrim($this->directory, '/') . '/thumbs/' . $info['filename'] . '_' . $info['extension']);

            // Delete image from database
            $Gallery_image = new Gallery_images_model();
            $Gallery_image->get_by_id($id);
            $Gallery_image->delete_all();
            unset($Gallery_image);
        }

        if ( ! empty($insert_records))
        {
            $Gallery_image_sort = new Gallery_images_model();
            $sort = $Gallery_image_sort->select_func('MAX', '@sort', 'max_sort')->where('gallery_id', $this->id)->get()->max_sort;

            foreach($insert_records as $filename)
            {
                $Gallery_image = new Gallery_images_model();
                $Gallery_image->filename = $filename;
                $Gallery_image->gallery_id = $this->id;

                $info = pathinfo($filename);
                $Gallery_image->title = ucwords(str_replace(array('_', '-'), ' ', $info['filename']));

                $sort++;
                $Gallery_image->sort = $sort;
                $Gallery_image->save();
                unset($Gallery_image);
            }
        }
    }
}
