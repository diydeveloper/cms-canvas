<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Snippets extends Admin_Controller {

	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'Snippets'));
        $this->load->model('snippets_model');

        $data['Snippets'] = $this->snippets_model
            ->order_by(($this->input->get('sort')) ? $this->input->get('sort') : 'title', ($this->input->get('order')) ? $this->input->get('order') : 'asc')
            ->get();

        $this->template->view('admin/snippets/snippets', $data);
	}

    function edit()
    {
        // Init
        $data = array();
        $data['edit_mode'] = FALSE;
        $this->template->add_package(array('codemirror'));
        $data['breadcrumb'] = set_crumbs(array('content/snippets' => 'Snippets', current_url() => 'Snippet Edit'));
        $data['revision_id'] = $revision_id = $this->uri->segment(6);
        $this->load->model('snippets_model');
        $data['Snippet'] = $Snippet = new Snippets_model();

        $snippet_id = $this->uri->segment(5);

        // Edit mode
        if ($snippet_id)
        {
            $data['edit_mode'] = TRUE;
            $Snippet->get_by_id($snippet_id);

            // Check if snippet exists
            if ( ! $Snippet->exists())
            {
                return show_404();
            }

            // Load a revision if a revision id was provided in the URL
            if ( ! empty($revision_id))
            {
                $this->load->model('revisions_model');
                $Revision = new Revisions_model();
                $Revision->get_by_id($revision_id);

                if ($Revision->exists())
                {
                    $revision_data = @unserialize($Revision->revision_data);
                    $Snippet->from_array($revision_data);
                }
                else
                {
                    return show_404();
                }
            }
        }

        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('short_name', 'Short Name', 'trim|required|alpha_dash|max_length[50]|is_unique[snippets.short_name.id.' . $snippet_id . ']');
        $this->form_validation->set_rules('snippet', 'Snippet', '');

        // Form validation
        if ($this->form_validation->run() == TRUE)
        {
            $Snippet->from_array($this->input->post());
            if ($edit_mode) {
                $Snippet->id = $snippet_id;
            }
            $Snippet->save();

            $Snippet->add_revision();

            // Clear cache
            $this->load->library('cache');
            $this->cache->delete_all('snippets');

            $this->session->set_flashdata('message', '<p class="success">Snippet Saved.</p>');

            if ($this->input->post('save_exit'))
            {
                redirect(ADMIN_PATH . '/content/snippets/');
            }
            else
            {
                redirect(ADMIN_PATH . '/content/snippets/edit/' . $Snippet->id);
            }
        }

        $this->template->view('admin/snippets/edit', $data);
    }

    function delete()
    {
        $this->load->model('snippets_model');

        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(5);
        }

        $Snippets = new Snippets_model();
        $Snippets->where_in('id', $selected)->get();

        if ($Snippets->exists())
        {
            foreach ($Snippets as $Snippet) {
                // Delete revisions
                $Revisions = $Snippet->get_revisions();
                $Revisions->delete_all();

                // Delete snippet
                $Snippet->delete();
            }

            // Clear cache
            $this->load->library('cache');
            $this->cache->delete_all('snippets');

            $message = '<p class="success">The selected items were successfully deleted.</p>';

            $this->session->set_flashdata('message', $message);
        }

        redirect(ADMIN_PATH . '/content/snippets');
    }
}

