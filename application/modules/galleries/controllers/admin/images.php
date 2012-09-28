<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Images extends Admin_Controller 
{
    public $model = 'gallery_images_model';
 
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $data = array();
        $gallery_id = $this->uri->segment(5);
        $data['breadcrumb'] = set_crumbs(array('galleries' => 'Galleries', current_url() => 'Images'));
        $this->template->add_package('tablednd'); 
        $data['Gallery'] = $Gallery = $this->load->model('galleries_model');

        $Gallery->get_by_id($gallery_id);

        if ( ! $Gallery->exists())
        {
            return show_404();
        }

        // Get data from db
        $data['Images'] = $Gallery->images->order_by('sort', 'ASC')->get_by_gallery_id($gallery_id);

        $_SESSION['KCFINDER'] = array();
        $_SESSION['KCFINDER']['disabled'] = false;
        $_SESSION['isLoggedIn'] = true;

        $this->template->view('admin/images/images', $data);
    }

    function add()
    {
        if (is_ajax() && $this->input->post('files') && $this->input->post('gallery_id'))
        {
            $this->load->model('gallery_images_model');

            // Get the max sort number for gallery
            $Gallery_image_sort = new Gallery_images_model();
            $sort = $Gallery_image_sort->select_func('MAX', '@sort', 'max_sort')->where('gallery_id', $this->input->post('gallery_id'))->get()->max_sort;

            // Insert selected images
            foreach($this->input->post('files') as $filename)
            {
                $Gallery_image = new Gallery_images_model();
                $Gallery_image->filename = urldecode($filename);
                $Gallery_image->gallery_id = $this->input->post('gallery_id');

                $info = pathinfo(urldecode($filename));
                $Gallery_image->title = ucwords(str_replace(array('_', '-'), ' ', $info['filename']));

                $sort++;
                $Gallery_image->sort = $sort;
                $Gallery_image->save();
                unset($Gallery_image);
            }
        }
        else
        {
            return show_404();
        }
    }

    function edit()
    {
        $data = array();
        $this->template->add_package(array('ckeditor', 'ck_jq_adapter'));
        $data['Image'] = $Image = $this->load->model('gallery_images_model');
        $image_id = $this->uri->segment(5);
        $Image->get_by_id($image_id);

        if ( ! $Image->exists())
        {
            return show_404();
        }

        $data['breadcrumb'] = set_crumbs(array('galleries' => 'Galleries', 'galleries/images/index/' . $Image->gallery_id => 'Images', current_url() => 'Image Edit'));
        
        // Validate Form
        $this->form_validation->set_rules('title', 'Title', "trim|required");
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('filename', 'filename', 'trim|required');
        $this->form_validation->set_rules('hide', 'Hide', 'trim|integer');

        if ($this->form_validation->run() == TRUE)
        {
            $Image->from_array($this->input->post()); 
            $Image->description = ($this->input->post('description') != '') ? $this->input->post('description') : NULL;
            $Image->alt = ($this->input->post('alt') != '') ? $this->input->post('alt') : NULL;
            $Image->hide = ($this->input->post('hide')) ? 1 : 0;
            $Image->save();

            $this->session->set_flashdata('message', '<p class="success">Image saved successfully.</p>');
            redirect(ADMIN_PATH . '/galleries/images/index/'.$Image->gallery_id); 
        }

        $_SESSION['KCFINDER'] = array();
        $_SESSION['KCFINDER']['disabled'] = false;
        $_SESSION['isLoggedIn'] = true;

        $this->template->view('admin/images/edit', $data);
    }

    function delete()
    {
        $this->load->model('gallery_images_model');

        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(5);
        }

        $Images = new Gallery_images_model();
        $Images->where_in('id', $selected)->get();

        if ($Images->exists())
        {
            $Images->delete_all();

            $this->session->set_flashdata('message', '<p class="success">The selected items were successfully deleted.</p>');
        }

        redirect(ADMIN_PATH . '/galleries/images/index/'.$this->uri->segment(5)); 
    }

    function order()
    {
        // Order images
        if (is_ajax())
        {
            if(count($_POST) > 0 && $this->input->post('image_table'))
            {
                $this->load->model('gallery_images_model');

                $table_order = $this->input->post('image_table');

                unset($table_order[0]);
                $table_order = array_values($table_order);

                $i = 1;
                foreach($table_order as $id)
                {
                    $Sort_images = new Gallery_images_model();
                    $Sort_images->get_by_id($id);
                    $Sort_images->sort = $i;
                    $Sort_images->save();
                    unset($Sort_images);

                    $i++;
                }
            }

            return;
        }
        else
        {
            return show_404();
        }
    }

    function create_thumb()
    {
        if (is_ajax())
        {
           if ($this->input->post('image_path'))
           {
               echo image_thumb($this->input->post('image_path'), 100, 100);
           }
        }
        else
        {
            return show_404();
        }
    }
}

