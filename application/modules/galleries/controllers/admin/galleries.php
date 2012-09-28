<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Galleries extends Admin_Controller 
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'Galleries'));
        $Galleries = $this->load->model('galleries_model');

        // Get data from db
        $data['Galleries'] = $Galleries->get();

        $this->template->view('admin/galleries/galleries', $data);
    }

    function edit()
    {
        $data['breadcrumb'] = set_crumbs(array('galleries' => 'Galleries', current_url() => 'Gallery Edit'));

        $data['Gallery'] = $Gallery = $this->load->model('galleries_model');
        $data['edit_mode'] = $edit_mode = FALSE;
        $gallery_id = $this->uri->segment(4);
        $this->load->helper('file');

        // Set Mode
        if ($gallery_id)
        {
            $data['edit_mode'] = $edit_mode = TRUE;
            $Gallery->get_by_id($gallery_id);

            if ( ! $Gallery->exists()) 
            {
                return show_404();
            }
        }
        
        // Validate Form
        $this->form_validation->set_rules('title', 'Title', "trim|required");

        if ($this->form_validation->run() == TRUE)
        {
            $Gallery->from_array($this->input->post());
            $Gallery->save();

            if ($edit_mode)
            {
                $this->session->set_flashdata('message', '<p class="success">Gallery saved successfully.</p>');
                redirect(ADMIN_PATH . '/galleries'); 
            }
            else
            {
                redirect(ADMIN_PATH . '/galleries/images/index/' . $Gallery->id); 
            }
        }


        $this->template->view('admin/galleries/edit', $data);
    }

    function delete()
    {
        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(4);
        }

        $this->load->model('galleries_model');
        $Galleries = $this->galleries_model->where_in('id', $selected)->get();

        if ($Galleries->exists())
        {
            foreach($Galleries as $Gallery)
            {
                $Gallery->images->get()->delete_all();
                $Gallery->delete();
            }

            $this->session->set_flashdata('message', '<p class="success">Gallery was deleted successfully.</p>');
        }

        redirect(ADMIN_PATH . '/galleries'); 
    }
}

