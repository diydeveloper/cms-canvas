<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Galleries_plugin extends Plugin
{
    public $images = array();

    /*
     * Gallery
     *
     * Builds array of images to use for custom content
     *
     * @return array
     */
    public function gallery()
    {
        $this->_build_image_array();
        
        return $this->images;
    }

    // ------------------------------------------------------------------------

    /*
     * Initialize
     *
     * Queries and builds array of gallery images and thumbs
     *
     * @access private
     * @return void
     */
    private function _build_image_array()
    {
        $this->Gallery = $this->load->model('galleries_model');

        $this->Gallery->get_by_id($this->attribute('gallery_id'));

        if ( ! $this->Gallery->exists())
        {
            return;
        }

        $Images = $this->Gallery->images->where('hide', 0)->order_by('sort', 'ASC')->get();
        
        foreach($Images as $Image)
        {
            $this->images[] = array(
                'title'       => $Image->title,
                'alt'         => $Image->alt,
                'description' => $Image->description,
                'image'       => $Image->filename,
            );
        }
    }

}

