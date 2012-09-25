<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Categories extends Admin_Controller {

	function __construct()
	{
		parent::__construct();	
	}
	
	function tree()
	{
        // Init
        $data = array();
        $data['breadcrumb'] = set_crumbs(array('content/categories/groups' => 'Category Groups', current_url() => 'Categories'));
        $this->template->add_package('nestedSortable');
        $data['Group'] = $Group = $this->load->model('category_groups_model');
        $data['Categories'] = $Categories = $this->load->model('categories_model');
        $group_id = $this->uri->segment(5);

        $Group->get_by_id($group_id);

        // Ensure that group exists
        if ( ! $Group->exists())
        {
            return show_404();
        }

        // Build categories tree
        $this->load->library('categories_library');
        $config['id'] = $group_id;
        $config['admin_categories'] = TRUE;
        $data['Tree'] = $this->categories_library->list_categories($config);

        $this->template->view('admin/categories/categories/categories', $data);
	}

    function edit()
    {
        // Init
        $data = array();
        $data['Group'] = $Group = $this->load->model('category_groups_model');
        $data['Category'] = $Category = $this->load->model('categories_model');
        $category_group_id = $this->uri->segment(5);
        $category_id = $this->uri->segment(6);
        $data['breadcrumb'] = set_crumbs(array('content/categories/groups' => 'Category Groups', 'content/categories/tree/' . $category_group_id => 'Categories Tree', current_url() => 'Category Edit'));
        $data['edit_mode'] = $edit_mode = FALSE;

        $Group->get_by_id($category_group_id);

        // Make sure a group with this id exists
        if ( ! $Group->exists())
        {
            return show_404();
        }

        // Set mode
        if ($category_id)
        {
            $Category->get_by_id($category_id);

            if ( ! $Category->exists())
            {
                return show_404();
            }

            $data['edit_mode'] = $edit_mode = TRUE;
        }

        // Form validation rules 
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('url_title', 'URL Title', 'trim|required|alpha_dash|max_length[255]|callback_unique_url_title' . (($edit_mode) ? '[' . $Category->url_title . ']' : ''));
        $this->form_validation->set_rules('tag_id', 'Tag ID', 'trim');
        $this->form_validation->set_rules('class', 'Class', 'trim');
        $this->form_validation->set_rules('target', 'Target', 'trim');
        $this->form_validation->set_rules('disable_current', 'Disable Current', 'trim');
        $this->form_validation->set_rules('disable_current_trail', 'Disable Current Trail', 'trim');

        if ($this->form_validation->run() == TRUE)
        {
            $Category->from_array($this->input->post());
            $Category->category_group_id = $category_group_id;
            $Category->hide = ($this->input->post('hide')) ? $this->input->post('hide') : 0;
            $Category->target = ($this->input->post('target')) ? $this->input->post('target') : NULL;
            $Category->tag_id = ($this->input->post('tag_id')) ? $this->input->post('tag_id') : NULL;
            $Category->class = ($this->input->post('class')) ? $this->input->post('class') : NULL;
            $Category->save();

            // Set the sort to the id of the category if inserting new category
            if ( ! $edit_mode)
            {
                $Category->sort = $Category->id;
                $Category->save();
            }

            // Clear categories cache so updates will show on next page load
            $this->load->library('categories_library');
            $this->categories_library->clear_cache();

            $this->session->set_flashdata('message', '<p class="success">Category Saved</p>');
            redirect(ADMIN_PATH . "/content/categories/tree/" . $category_group_id);
        }

        $this->template->view('admin/categories/categories/edit', $data);
    }

    function save_tree()
    {
        // Allow only ajax calls
        if ( ! is_ajax()) 
        {
            return show_404();
        }

        $this->load->model('categories_model');

        $list = $this->input->post('list');

        // Update db with order posted
        $i = 0;
        foreach($list as $id=>$parent_id)
        {
            $Category = new Categories_model();

            $node_info_array = array();
            $node_info_array['id'] = $id;
            $node_info_array['parent_id'] = ($parent_id == 'root') ? 0 : $parent_id;
            $node_info_array['sort'] = $i;

            // Save Node
            $Category->from_array($node_info_array);
            $Category->save();
            $i++;
        }

        // Clear categories cache so updates will show on next page load
        $this->load->library('categories_library');
        $this->categories_library->clear_cache();
    }

    function delete()
    {
        $category_group_id = null;

        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(5);
        }

        $Categories =& $this->load->model('categories_model');
        $Categories->where_in('id', $selected)->get();

        if ($Categories->exists())
        {
            $category_group_id = $Categories->category_group_id;

            foreach($Categories as $Category)
            {
                // Delete category's relationship to entries
                $Category->entries->get();
                $Category->delete($Category->entries->all, 'entries');
                $Category->delete();
            }

            $this->session->set_flashdata('message', '<p class="success">Category group was deleted successfully.</p>');
        }

        // Clear categories cache so updates will show on next page load
        $this->load->library('categories_library');
        $this->categories_library->clear_cache();

        if ( ! empty($category_group_id))
        {
            redirect(ADMIN_PATH . '/content/categories/tree/' . $category_group_id); 
        }
        else
        {
            redirect(ADMIN_PATH . '/content/categories/groups'); 
        }
    }

    function unique_url_title($url_title, $current_url_title = '')
    {
        $Categories = new Categories_model();

        // If in edit mode ignore its current name
        if ($current_url_title != '')
        {
            $Categories->where('url_title !=', $current_url_title);
        }

        $Categories->where('category_group_id', $this->uri->segment(5))
            ->where('url_title', $url_title)
            ->get();

        if ($Categories->exists())
        {
            $this->form_validation->set_message('unique_url_title', 'The %s provided is already in use by anohter category in this category group.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    /* 
     ------------------------------------------------------------------------
     * Groups
     ------------------------------------------------------------------------
     */

	function groups()
	{
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'Category Groups'));
        $Groups =& $this->load->model('category_groups_model');

        $data['Groups'] = $Groups
            ->order_by(($this->input->get('sort')) ? $this->input->get('sort') : 'title', ($this->input->get('order')) ? $this->input->get('order') : 'asc')
            ->get();

        $this->template->view('admin/categories/groups/groups', $data);
	}

    function group_edit()
    {
        // Init
        $data = array();
        $data['breadcrumb'] = set_crumbs(array('content/categories/groups' => 'Category Groups', current_url() => 'Category Group Edit'));
        $data['Group'] = $Group = $this->load->model('category_groups_model');
        $data['edit_mode'] = $edit_mode = FALSE;

        $group_id = $this->uri->segment(5);

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

            $this->session->set_flashdata('message', '<p class="success">Category Group Saved.<p>');

            if ($edit_mode)
            {
                redirect(ADMIN_PATH . '/content/categories/groups');
            }
            else
            {
                redirect(ADMIN_PATH . '/content/categories/tree/' . $Group->id);
            }
        }

        $this->template->view('admin/categories/groups/edit', $data);
    }

    function group_delete()
    {
        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(5);
        }

        $Groups =& $this->load->model('category_groups_model');
        $Groups->where_in('id', $selected)->get();

        if ($Groups->exists())
        {
            $this->load->model('content_types_model');

            foreach($Groups as $Group)
            {
                // Clear category group relations to content types
                $Content_types = new Content_types_model();
                $Content_types->where('category_group_id', $Group->id)->update('category_group_id', NULL);

                $Group->categories->get();

                foreach ($Group->categories as $Category)
                {
                    // Delete category's relationship to entries
                    $Category->entries->get();
                    $Category->delete($Category->entries->all, 'entries');
                    $Category->delete();
                }

                $Group->delete();
            }

            $this->session->set_flashdata('message', '<p class="success">Category group was deleted successfully.</p>');
        }

        // Clear categories cache so updates will show on next page load
        $this->load->library('categories_library');
        $this->categories_library->clear_cache();

        redirect(ADMIN_PATH . '/content/categories/groups'); 
    }
}

