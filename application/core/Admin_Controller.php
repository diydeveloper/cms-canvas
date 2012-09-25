<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Admin_Controller extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

        $this->load->helper('admin_helper');

        $this->template->set_theme('admin', 'default', 'application/themes');
        $this->template->parse_views = FALSE;

        $this->secure
            ->group_types(array(ADMINISTRATOR))
            ->unauthenticated_redirect(ADMIN_PATH . '/users/login')
            ->require_auth();

        // Load jQuery by default
        $this->template->add_package(array('jquery', 'jquerytools', 'admin_jqueryui'));
	}

    public function _remap($method, $params = array())
    {
        // Check group type Administrator's permissions for access
        if ($this->Group_session->type == ADMINISTRATOR)
        {
            $permissions = unserialize($this->Group_session->permissions);
            $access_options = unserialize(ADMIN_ACCESS_OPTIONS);

            // If page is set as a permission access option but not in groups permissions, show permission denied
            if ( ( ! isset($permissions['access']) || ! in_uri($permissions['access'])) && in_uri($access_options, null, TRUE))
            {
                // Access forbidden:
                header('HTTP/1.1 403 Forbidden');

                return $this->template->view('users/admin/permission_denied');
            }
        }

        // User has permission, continue like normal
        $method = $method;

        if (method_exists($this, $method))
        {
            return call_user_func_array(array($this, $method), $params);
        }

        show_404();
    }
}
