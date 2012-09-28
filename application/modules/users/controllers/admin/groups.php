<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Groups extends Admin_Controller 
{
 
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'User Groups'));
        $this->load->library('pagination');
        $this->load->model('groups_model');
        $data['query_string'] = ( ! empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : '';

        // Hide super admin groups from other groups
        if ($this->Group_session->type != SUPER_ADMIN)
        {
            $this->groups_model->where('type !=', SUPER_ADMIN);
        }

        $per_page = 50;

        // Query
        $data['Groups'] = $this->groups_model->order_by(($this->input->get('sort')) ? $this->input->get('sort') : 'name', ($this->input->get('order')) ? $this->input->get('order') : 'asc')->get_paged($this->uri->segment(5), $per_page, TRUE);

        $config['base_url'] = site_url(ADMIN_PATH . '/users/groups/index/');
        $config['per_page'] = $per_page;
        $config['uri_segment'] = '5';
        $config['num_links'] = 5;
        $config['suffix'] = $data['query_string'];
        $config['total_rows'] = $data['Groups']->paged->total_rows;

        $this->pagination->initialize($config); 

        $this->template->view('admin/groups/groups', $data);
    }

    function edit()
    {
        // Init
        $data = array();
        $data['breadcrumb'] = set_crumbs(array('users/groups' => 'User Groups', current_url() => 'Group Edit'));
        $data['Group'] = $Group = $this->load->model('groups_model');
        $data['permissions'] = array();
        $group_id = $this->uri->segment(5);
        $data['edit_mode'] = $edit_mode = FALSE;
        $data['permission_options'] =  unserialize(ADMIN_ACCESS_OPTIONS);

        // Edit Mode
        if ($group_id)
        {
            $data['edit_mode'] = $edit_mode = TRUE;

            $Group->get_by_id($group_id);

            // Stop non-super admins from editing super admins
            if ($this->secure->get_group_session()->type != SUPER_ADMIN && $Group->type == SUPER_ADMIN)
            {
                show_404();
            }

            if ($Group->exists()) 
            {
                $data['permissions'] = unserialize($Group->permissions);
            }
            else
            {
                show_404();
            }
        }
        
        // Validate Form
        $this->form_validation->set_rules('name', 'Group Name', "trim|required|callback_name_check[$group_id]");
        $this->form_validation->set_rules('permissions[access][]', 'Access Permissions', "trim");

        if ($edit_mode && $Group->modifiable_permissions)
        {
            $this->form_validation->set_rules('type', 'Group Type', "trim|required");
        }
        else
        {
            $this->form_validation->set_rules('type', 'Group Type', "trim");
        }

        if ($this->form_validation->run() == TRUE)
        {
            $Group = new Groups_model();

            // Load group to update if in edit mode
            if ( ! empty($group_id))
            {
                $Group->get_by_id($group_id);
            }

            $Group->from_array($this->input->post());

            // If permissions posted serialize for db
            if ($this->input->post('permissions') && $this->input->post('type') == 'administrator')
            {
                $Group->permissions = serialize($this->input->post('permissions'));
            }
            elseif (($edit_mode && $data['Group']->modifiable_permissions) 
                ||  ! $edit_mode 
                || ($edit_mode && $Group->type == ADMINISTRATOR && $this->Group_session->type == SUPER_ADMIN)) // Don't clear permissions if group had modifiable permissions disabled
            {
                $Group->permissions = NULL;
            }

            // Save Changes
            $Group->save(); 

            $this->session->set_flashdata('message', '<p class="success">User Group Saved.</p>');

            redirect(ADMIN_PATH . '/users/groups'); 
        }

        $this->template->view('admin/groups/edit', $data);
    }

    function delete()
    {
        $this->load->model('groups_model');
        $message = '';

        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(5);
        }

        // Show error if default layout was requested to be deleted
        if (in_array($this->settings->users_module->default_group, $selected))
        {
            $Default_group = new Groups_model();
            $Default_group->get_by_id($this->settings->users_module->default_group);

            if ($Default_group->exists())
            {
                unset($selected[array_search($Default_group->id, $selected)]);
                $message .= '<p class="error">The group ' . $Default_group->name . ' is set as the default group and cannot be deleted.</p>';
            }
        }

        // Check if any of the selected are required
        $Required_groups = new Groups_model();
        if ( ! empty($selected))
        {
            $Required_groups->where('required', 1)->where_in('id', $selected)->get();
        }

        foreach ($Required_groups as $Required_group)
        {
            unset($selected[array_search($Required_group->id, $selected)]);
            $message .= '<p class="error">The group ' . $Required_group->name . ' is required by the system and cannot be deleted.</p>';
        }

        // Check if any of the selected are associated to pages
        $Associated_groups = new Groups_model();
        if ( ! empty($selected))
        {
            $Associated_groups->where_in_related('users', 'group_id', $selected)->group_by('group_id')->get();
        }

        foreach ($Associated_groups as $Associated_group)
        {
            unset($selected[array_search($Associated_group->id, $selected)]);
            $message .= '<p class="error">The group ' . $Associated_group->name . ' is associated to one or more users and cannot be deleted.</p>';
        }

        $Group = new Groups_model();

        // Non-super admins cannot delete super admins nor can they delete themselves
        if ( ! empty($selected))
        {
            if ($this->Group_session->type == SUPER_ADMIN)
            {
                $Group->where_in('id', $selected)->get();
            }
            else
            {
                $Group->where('type !=', SUPER_ADMIN)->where_in('id', $selected)->get();
            }

            if ($Group->exists())
            {
                $Group->delete_all();
                $message = '<p class="success">The selected items were successfully deleted.</p>' . $message;
            }
        }

        $this->session->set_flashdata('message', $message);

        redirect(ADMIN_PATH . '/users/groups'); 
    }

    /*
     * Form Validation callback to check if group name already exists.
     */
    function name_check($name, $group_id)
    {
        $this->load->model('groups_model');
        $Group = new Groups_model();

        $Group->where("name = '$name'")->get();

        if ($Group->exists() && $Group->id != $group_id)
        {
            $this->form_validation->set_message('name_check', "This group name already exists.");
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

}

