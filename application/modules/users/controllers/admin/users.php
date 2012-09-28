<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Users extends Admin_Controller 
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'Users'));
        $this->load->library('pagination');
        $this->load->model('users_model');
        $this->load->model('groups_model');

        $data['query_string'] = ( ! empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : '';

        // Hide super admin users from everyone but super admins
        if ($this->Group_session->type != SUPER_ADMIN)
        {
            $this->groups_model->where('type !=', SUPER_ADMIN);
            $this->users_model->where_related_groups('type !=', SUPER_ADMIN);
        }

        // Get groups for group filter
        $data['Groups'] = $this->groups_model->get();

        // Process Filter Using Admin Helper
        $filter = process_filter('users');

        // Filter results by search query
        if (isset($filter['search']))
        {
            $this->users_model->where("(concat_ws(' ', first_name, last_name) LIKE '%{$filter['search']}%' OR email LIKE '%{$filter['search']}%')");
        }

        // Filter results by group
        if (isset($filter['group_id']))
        {
            $this->users_model->where("group_id", $filter['group_id']);
        }

        $per_page = 50;

        // Query
        $data['Users'] = $this->users_model->order_by(($this->input->get('sort')) ? $this->input->get('sort') : 'last_name', ($this->input->get('order')) ? $this->input->get('order') : 'asc')->include_related('groups', 'name')->get_paged($this->uri->segment(4), $per_page, TRUE);

        $config['base_url'] = site_url(ADMIN_PATH . '/users/index/');
        $config['per_page'] = $per_page;
        $config['uri_segment'] = '4';
        $config['num_links'] = 5;
        $config['suffix'] = $data['query_string'];
        $config['total_rows'] = $data['Users']->paged->total_rows;

        $this->pagination->initialize($config); 

        $this->template->view('admin/users/users', $data);
    }

    function edit()
    {
        // Init
        $data = array();
        $data['breadcrumb'] = set_crumbs(array('users' => 'Users', current_url() => 'User Edit'));
        $data['User'] = $User = $this->load->model('users_model');
        $data['states'] = unserialize(STATES);
        $data['edit_mode'] = $edit_mode = FALSE;
        $user_id = $this->uri->segment(4);

        // Edit Mode 
        if ($user_id)
        {
            $data['edit_mode'] = $edit_mode = TRUE;

            $User->include_related('groups', 'type')->get_by_id($user_id);

            // Stop non-super users from editing super users
            if ($this->Group_session->type != SUPER_ADMIN && $data['User']->groups_type == SUPER_ADMIN)
            {
                show_404();
            }

            if ( ! $User->exists()) 
            {
                show_404();
            }
        }
        
        // Validate Form
        $this->form_validation->set_rules('email', 'Email', "trim|required|valid_email|callback_email_check[$user_id]");
        $this->form_validation->set_rules('group_id', 'Group', 'trim|required');
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|format_phone');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('address2', 'Address 2', 'trim');
        $this->form_validation->set_rules('city', 'City', 'trim');
        $this->form_validation->set_rules('state', 'State', 'trim');
        $this->form_validation->set_rules('zip', 'Zip', 'trim');

        if ($edit_mode)
        {
            $this->form_validation->set_rules('password', 'Password', 'trim');

            if ($this->input->post('password'))
            {
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
            }
        }
        else
        {
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
        }

        // Process Form
        if ($this->form_validation->run() == TRUE)
        {
            if ( ! $this->input->post('password'))
            {
                unset($_POST['password']);
            }

            $User->from_array($this->input->post());

            if ($this->input->post('password'))
            {
                $User->password = md5($this->config->item('encryption_key') . $this->input->post('password'));
            }

            // Set a created date if new user
            if ( ! $edit_mode)
            {
                $User->created_date = date('Y-m-d H:i:s');
            }

            $User->save(); 

            $this->session->set_flashdata('message', '<p class="success">User saved.</p>');

            redirect(ADMIN_PATH . '/users'); 
        }

        // Get Groups From DB
        $this->load->model('groups_model');
        if($this->Group_session->type == SUPER_ADMIN)
        {
            $data['Groups'] = $this->groups_model->get();
        }
        else
        {
            $data['Groups'] = $this->groups_model->where('type !=', SUPER_ADMIN)->get();
        }

        $this->template->view('admin/users/edit', $data);
    }

    function delete()
    {
        $this->load->model('users_model');

        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(4);
        }

        $User = new Users_model();

        // Non-super admins cannot delete super admins nor can they delete themselves
        if ($this->Group_session->type == SUPER_ADMIN)
        {
            $User->where('id !=', $this->secure->get_user_session()->id)->where_in('id', $selected)->get();
        }
        else
        {
            $User->where('id !=', $this->secure->get_user_session()->id)->where_related_groups('type !=', SUPER_ADMIN)->where_in('id', $selected)->get();
        }

        if ($User->exists())
        {
            // Delete user uploads
            $this->load->helper('file');

            foreach ($User as $My_user)
            {
                $upload_path = FCPATH . USER_DATA . $My_user->id . '/';
                delete_files($upload_path, TRUE);
                @rmdir($upload_path);
            }

            $User->delete_all();

            $this->session->set_flashdata('message', '<p class="success">The selected items were successfully deleted.</p>');
        }

        redirect(ADMIN_PATH . '/users'); 
    }

    function resend_activation()
    {
        $user_id = $this->uri->segment('4');

        $this->load->model('users_model');
        $User = $this->users_model->get_by_id($user_id);
        
        if ( ! $User->exists())
        {
            return show_404();
        }

        $this->load->library('email');

        $this->email->from('noreply@' . domain_name(), $this->settings->site_name);
        $this->email->to($User->email);
        $this->email->subject($this->settings->site_name . ' Activation');
        $this->email->message("Thank you for your new member registration.\n\nTo activate your account, please visit the following URL\n\n" . site_url('users/activate/' . $User->id . '/' . $User->activation_code) . "\n\nThank You!\n\n" . $this->settings->site_name);
        $this->email->send();
    }

    function login_as_user()
    {
        $user_id = $this->uri->segment('4');

        $this->load->model('users_model');
        $User = $this->users_model->get_by_id($user_id);

        if ( ! $User->exists())
        {
            return show_404();
        }
        
        $User->create_session($this->secure->get_user_session()->id);

        redirect('/');
    }

    /*
     * Form Validation callback to check if an email address is already in use.
     */
    function email_check($email, $user_id)
    {
        $this->load->model('users_model');

        $User = new Users_model();
        $User->where("email = '$email'")->get();

        if ($User->exists() && $User->id != $user_id)
        {
            $this->form_validation->set_message('email_check', "This email address is already in use.");
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

}
