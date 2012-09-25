<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Items extends Admin_Controller {

	function __construct()
	{
		parent::__construct();	
	}

    function tree()
    {
        // Init
        $data = array();
        $data['breadcrumb'] = set_crumbs(array('navigations' => 'Navigations', current_url() => 'Navigation Tree'));
        $this->template->add_package('nestedSortable');
        $Navigation = $this->load->model('navigations_model');
        $data['navigation_id'] = $navigation_id = $this->uri->segment(5);

        $data['Navigation'] = $Navigation->get_by_id($navigation_id);

        // Validate that a naviagation exists with this navigation id
        if ( ! $Navigation->exists())
        {
            return show_404();
        }

        // Build navigation tree using library
        $this->load->library('navigations_library');
        $config['id'] = $navigation_id;
        $config['admin_nav'] = TRUE;
        $data['Tree'] = $this->navigations_library->list_nav($config);

        $this->template->view('admin/items/tree', $data);
    }

    function edit()
    {
        // Init
        $data = array();
        $data['Navigation'] = $Navigation = $this->load->model('navigations_model');
        $data['Navigation_item'] = $Navigation_item = $this->load->model('navigation_items_model');
        $data['navigation_id'] = $navigation_id = $this->uri->segment(5);
        $data['breadcrumb'] = set_crumbs(array('navigations' => 'Navigations', 'navigations/items/tree/' . $navigation_id => 'Navigation Tree', current_url() => 'Navigation Item Edit'));
        $data['edit_mode'] = $edit_mode = FALSE;
        $item_id = $this->uri->segment(6);

        // Verify navigation exists
        $Navigation->get_by_id($navigation_id);

        if ( ! $Navigation->exists())
        {
            return show_404();
        }

        // Set mode
        if ($item_id)
        {
            $Navigation_item->get_by_id($item_id);

            if ( ! $Navigation_item->exists())
            {
                return show_404();
            }

            $data['edit_mode'] = $edit_mode = TRUE;
        }

        // Get all entries for link dropdown
        $this->load->model('content/entries_model');
        $Pages = $this->entries_model
            ->where('status', 'published')
            ->where('slug !=', 'NULL')
            ->or_where('id =', $this->settings->content_module->site_homepage)
            ->order_by('title')
            ->get();
        $data['Pages'] = option_array_value($Pages, 'id', 'title', array(''  => ''));

        // Form validation rules 
        if ($this->input->post('type') == 'page')
        {
            $this->form_validation->set_rules('entry_id', 'Pages', 'trim|required');
            $this->form_validation->set_rules('page_link_text', 'Link Text', 'trim');
        }
        else
        {
            $this->form_validation->set_rules('title', 'Link Text', 'trim|required');
            $this->form_validation->set_rules('url', 'URL', 'trim|required');
        }

        $this->form_validation->set_rules('type', 'Link Type', 'trim|required');
        $this->form_validation->set_rules('tag_id', 'Tag ID', 'trim');
        $this->form_validation->set_rules('class', 'Class', 'trim');
        $this->form_validation->set_rules('target', 'Target', 'trim');
        $this->form_validation->set_rules('disable_current', 'Disable Current', 'trim');
        $this->form_validation->set_rules('disable_current_trail', 'Disable Current Trail', 'trim');

        if ($this->form_validation->run() == TRUE)
        {
            $Navigation_item->from_array($this->input->post());
            $Navigation_item->navigation_id = $navigation_id;

            if ($this->input->post('type') == 'page')
            {
                $Navigation_item->title = ($this->input->post('page_link_text')) ? $this->input->post('page_link_text') : NULL;
                $Navigation_item->url = NULL;
            }
            else if ($this->input->post('type') == 'dynamic_route')
            {
                $Navigation_item->url = trim($this->input->post('url'), '/');
            }
            else
            {
                $Navigation_item->entry_id = NULL;
            }

            $Navigation_item->tag_id = ($this->input->post('tag_id')) ? $this->input->post('tag_id') : NULL;
            $Navigation_item->class = ($this->input->post('class')) ? $this->input->post('class') : NULL;
            $Navigation_item->target = ($this->input->post('target')) ? $this->input->post('target') : NULL;
            $Navigation_item->hide = ($this->input->post('hide')) ? 1 : 0;
            $Navigation_item->disable_current = ($this->input->post('disable_current')) ? 1 : 0;
            $Navigation_item->disable_current_trail = ($this->input->post('disable_current_trail')) ? 1 : 0;

            $Navigation_item->save();

            // Set the sort to the id if creating new item
            if ( ! $edit_mode)
            {
                $Navigation_item->sort = $Navigation_item->id;
                $Navigation_item->save();
            }

            // Clear navigation cache so updates will show on next page load
            $this->load->library('navigations_library');
            $this->navigations_library->clear_cache();

            $this->session->set_flashdata('message', '<p class="success">Navigation Item Saved</p>');
            redirect(ADMIN_PATH . "/navigations/items/tree/$navigation_id");
        }

        $this->template->view('admin/items/edit', $data);
    }

    function delete($Item_object = null)
    {
        if($item_id = $this->uri->segment(5))
        {
            $this->load->model('navigation_items_model');

            if (is_object($Item_object))
            {
                $Item = $Item_object;
            }
            else
            {
                $Item = new Navigation_items_model();
                $Item->get_by_id($item_id);
                $navigation_id = $Item->navigation_id;
            }

            $Children = new Navigation_items_model();
            $Children->get_by_parent_id($Item->id);

            foreach($Children as $Child)
            {
                // Delete Node
                $this->delete($Child);
            }

            $Item->delete();

            if (is_object($Item_object))
            {
                return;
            }
        }

        // Clear navigation cache so updates will show on next page load
        $this->load->library('navigations_library');
        $this->navigations_library->clear_cache();

        $this->session->set_flashdata('message', '<p class="success">Navigation Item(s) Deleted</p>');

        if (isset($navigation_id))
        {
            redirect(ADMIN_PATH . "/navigations/items/tree/$navigation_id");
        }
        else
        {
            redirect(ADMIN_PATH . "/navigations");
        }
    }

    function save_tree()
    {
        if ( ! is_ajax()) 
        {
            return show_404();
        }

        $this->load->model('navigation_items_model');

        $list = $_POST['list'];

        $i = 0;
        foreach($list as $id=>$parent_id)
        {
            $Navigation_items_object = new Navigation_items_model();

            $node_info_array = array();
            $node_info_array['id'] = $id;
            $node_info_array['parent_id'] = ($parent_id == 'root') ? 0 : $parent_id;
            $node_info_array['sort'] = $i;

            // Save Node
            $Navigation_items_object->from_array($node_info_array);
            $Navigation_items_object->save();
            unset($Navigation_items_object);
            $i++;
        }

        // Clear navigation cache so updates will show on next page load
        $this->load->library('navigations_library');
        $this->navigations_library->clear_cache();
    }
	
}

