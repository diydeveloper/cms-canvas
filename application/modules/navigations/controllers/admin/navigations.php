<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Navigations extends Admin_Controller {

	function __construct()
	{
		parent::__construct();	
	}

	function index()
	{
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'Navigations'));
        $Groups = $this->load->model('navigations_model');

        $data['Groups'] = $Groups
            ->order_by(($this->input->get('sort')) ? $this->input->get('sort') : 'title', ($this->input->get('order')) ? $this->input->get('order') : 'asc')
            ->get();

        $this->template->view('admin/navigations/navigations', $data);
	}

    function edit()
    {
        // Init
        $data = array();
        $data['breadcrumb'] = set_crumbs(array('navigations/groups' => 'Navigations', current_url() => 'Navigation Edit'));
        $data['Group'] = $Group = $this->load->model('navigations_model');
        $data['edit_mode'] = $edit_mode = FALSE;

        $group_id = $this->uri->segment(4);

        if ( ! empty($group_id))
        {
            $data['edit_mode'] = $edit_mode = TRUE;
            $Group->get_by_id($group_id);

            if ( ! $Group->exists())
            {
                return show_404();
            }
        }

        $this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[255]');

        // Form validation
        if ($this->form_validation->run() == TRUE)
        {
            $Group->from_array($this->input->post());
            $Group->save();

            $this->session->set_flashdata('message', '<p class="success">Navigation Saved.<p>');

            if ($edit_mode)
            {
                redirect(ADMIN_PATH . '/navigations');
            }
            else
            {
                redirect(ADMIN_PATH . '/navigations/items/tree/' . $Group->id);
            }
        }

        $this->template->view('admin/navigations/edit', $data);
    }

    function delete()
    {
        $this->load->model('navigations_model');

        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(4);
        }

        $Navigation = new Navigations_model();
        $Navigation->where('required', 0)->where_in('id', $selected)->get();

        $Required_navigation = new Navigations_model();
        $Required_navigation->where('required', 1)->where_in('id', $selected)->get();

        if ($Navigation->exists())
        {
            // Delete navigation items associated to navigation
            $this->load->model('navigation_items_model');
            $Navigation_items = $this->navigation_items_model->where_in('navigation_id', $Navigation)->get();
            $Navigation_items->delete_all();

            $Navigation->delete_all();

            // Clear navigation cache so updates will show on next page load
            $this->load->library('navigations_library');
            $this->navigations_library->clear_cache();

            $this->session->set_flashdata('message', '<p class="success">The selected items were successfully deleted.</p>');
        }

        // Show error if any required navigations were requested to be deleted
        if ($Required_navigation->exists())
        {
            $this->session->set_flashdata('message', '<p class="attention">One or more of the selected navigations are required by the system and could not be deleted.</p>');
        }

        redirect(ADMIN_PATH . '/navigations');
    }
}

