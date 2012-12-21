<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

        // Load in the admin helper functions if the current user is an administrator
        if ($this->secure->group_types(array(ADMINISTRATOR))->is_auth())
        {
            $this->load->helper('admin_helper');
        }

        $this->cms_parameters = array();
        $this->cms_base_route = '';

        // Check if to force ssl on controller
        if (in_uri($this->config->item('ssl_pages')))
        {
            force_ssl();
        } 
        else 
        {
            remove_ssl();
        }

        // Create Dynamic Page Title
        if ( ! $title = str_replace('-', ' ', $this->uri->segment(1)))
        {
            $title = 'Home';
        }

        if ($segment2 = str_replace('-', ' ', $this->uri->segment(2)))
        {
            $title = $segment2 . " - " . $title;
        }

        $this->template->set_meta_title(ucwords($title) . " | " . $this->settings->site_name);

        // Set Group
        if ($this->session->userdata('user_session'))
        {
            $this->group_id = $this->session->userdata('user_session')->group_id;
            $this->Group_session = $this->session->userdata('group_session');
        }
	}
}
