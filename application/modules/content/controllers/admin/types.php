<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Types extends Admin_Controller {

	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'Content Types'));
        $this->load->model('content_types_model');

        $data['Types'] = $this->content_types_model
            ->order_by(($this->input->get('sort')) ? $this->input->get('sort') : 'title', ($this->input->get('order')) ? $this->input->get('order') : 'asc')
            ->get();

        $this->template->view('admin/types/types', $data);
	}

    function edit()
    {
        // Init
        $data = array();
        $data['breadcrumb'] = set_crumbs(array('content/types' => 'Content Types', current_url() => 'Content Type Edit'));
        $this->load->model('content_types_model');
        $this->load->model('content_types_admin_groups_model');
        $this->load->model('users/groups_model');
        $this->load->model('category_groups_model');

        // Load codemirror
        $this->template->add_package(array('codemirror', 'zclip'));

        $content_type_id = $this->uri->segment(5);

        $data['Content_type'] = $Content_type = $this->content_types_model->get_by_id($content_type_id);
        $data['theme_layouts'] = $this->template->get_theme_layouts($this->settings->theme, TRUE);
        $data['Admin_groups'] = $Admin_groups = $this->groups_model->where('type', ADMINISTRATOR)->order_by('name')->get();

        // Get all groups except super admin for group access
        $Groups = new Groups_model();
        $data['Groups'] = $Groups->where('type !=', SUPER_ADMIN)->order_by('name')->get();

        // Get all category groups for dropdown
        $data['category_groups'] = option_array_value($this->category_groups_model->order_by('title')->get(), 'id', 'title', array('' => ''));

        // Check if layout exists
        if ( ! $data['Content_type']->exists())
        {
            return show_404();
        }

        // Get selected restrict to groups for repopulation
        $data['restrict_to'] = @unserialize($data['Content_type']->restrict_to);

        if ( ! is_array($data['restrict_to']))
        {
            $data['restrict_to'] = (array) $data['restrict_to'];
        }

        // Get admin groups currently assigned to this conten type
        $data['current_admin_groups'] = option_array_value($Content_type->admin_groups->get(), 'id', 'group_id');

        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('short_name', 'Short Name', 'trim|required|alpha_dash|max_length[50]|is_unique[content_types.short_name.id.' . $content_type_id . ']');
        $this->form_validation->set_rules('enable_dynamic_routing', 'Enable Dynamic Routing', 'trim');
        $this->form_validation->set_rules('enable_versioning', 'Enable Versioning', 'trim|required');
        $this->form_validation->set_rules('max_revisions', 'Max Revisions', 'trim|required|integer|less_than[26]');
        $this->form_validation->set_rules('category_group_id', 'Category Group', 'trim');
        $this->form_validation->set_rules('restrict_admin_access', 'Restrict Admin Access', 'trim|required');
        $this->form_validation->set_rules('selected_admin_groups[]', 'Administrative Access', 'trim');
        $this->form_validation->set_rules('access', 'Access', 'trim|required');
        $this->form_validation->set_rules('restrict_to[]', 'Group Access', 'trim');
        $this->form_validation->set_rules('entries_allowed', 'Number of Entries Allowed', 'trim|integer');

        $this->form_validation->set_rules('layout', 'layout', '');

        // Add dynamic route validation if enable dynamic routing checkbox selected
        if ($this->input->post('enable_dynamic_routing') == 1)
        {
            $this->form_validation->set_rules('dynamic_route', 'Dynamic Route', 'trim|required|max_length[255]|callback_unique_dynamic_route['. $Content_type->dynamic_route . ']');
        }

        // Form validation
        if ($this->form_validation->run() == TRUE)
        {
            // Deletect if the category group changed.
            // If it has changed delete entry category relations 
            // of the content type
            if ($Content_type->category_group_id != $this->input->post('category_group_id'))
            {
                $Content_type->entries->get();

                foreach ($Content_type->entries as $Entry)
                {
                    $Entry->categories->get();
                    $Entry->delete($Entry->categories->all, 'categories');
                }
            }

            $Content_type->from_array($this->input->post());
            $Content_type->id = $content_type_id;
            $Content_type->dynamic_route = ($this->input->post('dynamic_route') != '' && $this->input->post('enable_dynamic_routing')) ? $this->input->post('dynamic_route') : NULL;
            $Content_type->restrict_to = ($this->input->post('access') == 2) ? serialize($this->input->post('restrict_to')) : NULL;
            $Content_type->category_group_id = ($this->input->post('category_group_id') != '') ? $this->input->post('category_group_id') : NULL;
            $Content_type->layout = (trim($this->input->post('layout')) != '') ? $this->input->post('layout') : NULL;
            $Content_type->page_head = (trim($this->input->post('page_head')) != '') ? $this->input->post('page_head') : NULL;
            $Content_type->theme_layout = ($this->input->post('theme_layout') != '') ? $this->input->post('theme_layout') : NULL;
            $Content_type->entries_allowed = ($this->input->post('entries_allowed') != '') ? $this->input->post('entries_allowed') : NULL;
            $Content_type->save();

            // Assign admin groups to this content type 
            // if restrict admin access is enabled
            if ($this->content_types_model->restrict_admin_access && is_array($this->input->post('selected_admin_groups')))
            {
                $selected_admin_groups = $this->input->post('selected_admin_groups');

                foreach ($Admin_groups as $Admin_group)
                {
                    $Content_types_admin_groups = new Content_types_admin_groups_model();
                    $Content_types_admin_groups->where('content_type_id', $Content_type->id)
                        ->where('group_id', $Admin_group->id)
                        ->get();

                    if (in_array($Admin_group->id, $selected_admin_groups))
                    {
                        // Admin group was selected so Update or Insert to database
                        $Content_types_admin_groups->content_type_id = $Content_type->id;
                        $Content_types_admin_groups->group_id = $Admin_group->id;
                        $Content_types_admin_groups->save();
                    }
                    else
                    {
                        // Admin group was NOT selected so delete it
                        $Content_types_admin_groups->delete_all();
                    }
                }
            }
            else
            {
                // Restrict admin access was disabled so remove any assigned groups from database
                $Content_types_admin_groups = new Content_types_admin_groups_model();
                $Content_types_admin_groups->where('content_type_id', $this->content_types_model->id)
                    ->get();

                $Content_types_admin_groups->delete_all();
            }

            // Clear cache
            $this->load->library('cache');
            $this->cache->delete_all('entries');
            $this->cache->delete_all('content_types');

            $this->session->set_flashdata('message', '<p class="success">Content Type Saved.</p>');

            if ($this->input->post('save_exit'))
            {
                redirect(ADMIN_PATH . '/content/types/');
            }
            else
            {
                redirect(ADMIN_PATH . '/content/types/edit/'.$this->content_types_model->id);
            }
        }

        $data['Fields'] = $this->content_types_model->content_fields->order_by('sort')->get();

        $this->template->view('admin/types/edit', $data);
    }

    function delete()
    {
        $this->load->helper('file');
        $this->load->model('content_types_model');
        $this->load->model('content_types_admin_groups_model');
        $this->load->model('entries_model');

        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(5);
        }

        $Content_types = new Content_types_model();
        $Content_types->where_in('id', $selected)->get();

        if ($Content_types->exists())
        {
            $message = '';
            $content_types_deleted = FALSE;
            $this->load->model('content_fields_model');

            foreach($Content_types as $Content_type)
            {
                if ($Content_type->required)
                {
                    $message .= '<p class="error">Content type ' . $Content_type->title . ' (' . $Content_type->short_name . ') is required by the system and cannot be deleted.</p>';
                }
                else if($Content_type->entries->limit(1)->get()->exists())
                {
                    $message .= '<p class="error">Content type ' . $Content_type->title .' ('. $Content_type->short_name . ') is associated to one or more entries and cannot be deleted.</p>';
                }
                else
                {
                    // Delete content type fields and entries data coloumns
                    $Content_fields = new Content_fields_model();
                    $Content_fields->where('content_type_id', $Content_type->id)->get();

                    foreach ($Content_fields as $Content_field)
                    {
                        $Content_fields->drop_entries_column();
                        $Content_fields->delete();
                    }
                   
                    // Delete content type admin groups
                    $Content_types_admin_groups = new Content_types_admin_groups_model();
                    $Content_types_admin_groups->where('content_type_id', $Content_type->id)->get();
                    $Content_types_admin_groups->delete_all();

                    // Delete content type
                    $Content_type->delete();
                    $content_types_deleted = TRUE;
                }
            }

            if ($content_types_deleted)
            {
                // Clear cache
                $this->load->library('cache');
                $this->cache->delete_all('entries');

                $message = '<p class="success">The selected items were successfully deleted.</p>' . $message;
            }


            $this->session->set_flashdata('message', $message);
        }

        redirect(ADMIN_PATH . '/content/types');
    }

    function add()
    {
        // Init
        $data = array();
        $data['breadcrumb'] = set_crumbs(array('content/types' => 'Content Types', current_url() => 'Add Content Type'));

        $this->load->model('content_types_model');
        $this->load->model('content_types_admin_groups_model');
        $this->load->model('content_fields_model');
        $this->load->model('users/groups_model');
        $this->load->model('category_groups_model');

        // Get theme layouts for theme layout dropdown
        $data['theme_layouts'] = $this->template->get_theme_layouts($this->settings->theme, TRUE);

        // Get all admin groups for admin group access
        $data['Admin_groups'] = $this->groups_model->where('type', ADMINISTRATOR)->order_by('name')->get();

        // Get all user groups except super admin for group access
        $Groups = new Groups_model();
        $data['Groups'] = $Groups->where('type !=', SUPER_ADMIN)->order_by('name')->get();

        // Get all category groups for dropdown
        $data['category_groups'] = option_array_value($this->category_groups_model->order_by('title')->get(), 'id', 'title', array('' => ''));

        // Form Validation
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('short_name', 'Short Name', 'trim|required|alpha_dash|max_length[50]|is_unique[content_types.short_name]');
        $this->form_validation->set_rules('enable_dynamic_routing', 'Enable Dynamic Routing', 'trim');
        $this->form_validation->set_rules('enable_versioning', 'Enable Versioning', 'trim|required');
        $this->form_validation->set_rules('max_revisions', 'Max Revisions', 'trim|required|integer|less_than[26]');
        $this->form_validation->set_rules('category_group_id', 'Category Group', 'trim');
        $this->form_validation->set_rules('restrict_admin_access', 'Restrict Admin Access', 'trim|required');
        $this->form_validation->set_rules('selected_admin_groups[]', 'Administrative Access', 'trim');
        $this->form_validation->set_rules('access', 'Access', 'trim|required');
        $this->form_validation->set_rules('restrict_to[]', 'Group Access', 'trim');
        $this->form_validation->set_rules('entries_allowed', 'Number of Entries Allowed', 'trim|integer');

        // Add dynamic route validation if enable dynamic routing checkbox selected
        if ($this->input->post('enable_dynamic_routing') == 1)
        {
            $this->form_validation->set_rules('dynamic_route', 'Dynamic Route', 'trim|required|max_length[255]|callback_unique_dynamic_route');
        }

        if ($this->form_validation->run() == TRUE)
        {
            // Save new content type
            $Content_type = new Content_types_model();
            $Content_type->from_array($this->input->post());
            $Content_type->dynamic_route = ($this->input->post('dynamic_route') != '' && $this->input->post('enable_dynamic_routing')) ? $this->input->post('dynamic_route') : NULL;
            $Content_type->restrict_to = ($this->input->post('access') == 2) ? serialize($this->input->post('restrict_to')) : NULL;
            $Content_type->category_group_id = ($this->input->post('category_group_id') != '') ? $this->input->post('category_group_id') : NULL;
            $Content_type->theme_layout = ($this->input->post('theme_layout') != '') ? $this->input->post('theme_layout') : NULL;
            $Content_type->entries_allowed = ($this->input->post('entries_allowed') != '') ? $this->input->post('entries_allowed') : NULL;
            $Content_type->save();

            // Assign admin groups to this content type 
            // if restrict admin access is enabled
            if ($Content_type->restrict_admin_access && is_array($this->input->post('selected_admin_groups')))
            {
                $selected_admin_groups = $this->input->post('selected_admin_groups');

                foreach ($selected_admin_groups as $admin_group)
                {
                    $Content_types_admin_groups = new Content_types_admin_groups_model();
                    $Content_types_admin_groups->content_type_id = $Content_type->id;
                    $Content_types_admin_groups->group_id = $admin_group;
                    $Content_types_admin_groups->save();
                }
            }

            // Clear cache
            $this->load->library('cache');
            $this->cache->delete_all('entries');
            $this->cache->delete_all('content_types');

            redirect(ADMIN_PATH . "/content/types/edit/$Content_type->id");
        }

        $this->template->view('admin/types/add', $data);
    }

    function unique_dynamic_route($route, $current_route = '')
    {
        $route = trim($route, '/');

        $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
        $regex .= "(\:[0-9]{2,5})?"; // Port
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor 

        if (preg_match("/^$regex$/", base_url() . $route))
        {
            $Content_types = new Content_types_model();
            $Content_types->get_by_dynamic_route($route);

            if ($Content_types->exists() && $route != stripslashes($current_route))
            {
                $this->form_validation->set_message('unique_dynamic_route', 'This %s provided is already in use.');
                return FALSE;
            }
            else
            {
                return $route;
            }
        }
        else
        {
            $this->form_validation->set_message('unique_dynamic_route', 'The %s provided is not valid.');
            return FALSE;
        }
    }

}

