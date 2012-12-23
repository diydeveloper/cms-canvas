<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Secure
{
    public $CI;
    private $groups = array();
    private $group_types = array();
    private $method_exceptions = array();
    private $unpermitted_redirect = null;
    private $unauthenticated_redirect;

    public function __construct()
    {
        $this->CI =& get_instance(); 
        $this->unauthenticated_redirect();
    }

    // --------------------------------------------------------------------

    /*
     * Require Auth
     *
     * Checks to see if the user is permitted and if not redirects them to the location set in unauthenticated_redirect
     * 
     * @return bool or redirect
     */
    public function require_auth()
    {
        // Prevent IE users from caching secured pages
        if( ! isset($_SESSION)) 
        {
            session_start();
        }


        // checks to see if the method being accessed is an exception to the controller's authentication
        if ( ! empty($this->method_exceptions))
        {
            if (in_array($this->CI->uri->sement(2), $this->method_exceptions))
            {
                return true;
            }
        }

        // checks for session variable and whether the user has permission to view the controller requested
        if ($this->unpermitted_redirect('/')->is_auth())
        {
            return true;
        }

        $this->CI->session->set_userdata('redirect_to', $this->CI->uri->uri_string());

        redirect($this->unauthenticated_redirect);
    }

    // --------------------------------------------------------------------

    /*
     * Is Auth
     *
     * Checks that the user is authenticated had has permission
     *
     * @return bool
     */
    public function is_auth()
    {
        if ($this->get_user_session())
        {
            $permitted = true;

            // If logged in user not a super admin check permissions
            if ($this->get_group_session()->type != SUPER_ADMIN)
            {
                // Check if group type has permission
                if ( ! empty($this->group_types))
                {    
                    if ( ! in_array($this->get_group_session()->type, $this->group_types))
                    {
                        $permitted = false;
                    }
                }

                // Check if group has permission
                if ( ! empty($this->groups))
                {    
                    if ( ! in_array($this->get_user_session()->group_id, $this->groups))
                    {
                        $permitted = false;
                    }
                }
            }

            $unpermitted_redirect = $this->unpermitted_redirect;
            $this->reset();

            if ( ! $permitted)
            {
                if ( ! empty($unpermitted_redirect))
                {
                    redirect($unpermitted_redirect);
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return true;
            }
        }

        // User is not authenticated. Do one last check to see if user has a remember me cookie set
        if ($this->check_remember_me())
        {
            // A cookie was found and a new session was created.
            // Rerun is_auth to now check if the user has permission
            return $this->is_auth();
        }
        else
        {
            return false;
        }
    }

    // --------------------------------------------------------------------

    /*
     * Check Remember Me
     *
     * Checks if user has a remember me cookie set 
     * and logs user in if validation is true
     *
     * @return bool
     */
    function check_remember_me()
    {
        $Users_model = $this->CI->load->model('users/users_model');
        return $Users_model->check_remember_me();
    }

    // --------------------------------------------------------------------

    /*
     * Groups
     *
     * Specifies the groups that will have access
     *
     * @param array
     * @return object
     */
    public function groups($groups)
    {
        if ( ! is_array($groups))
        {
            $groups = (array) $groups;
        }

        $this->groups = $groups;

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Group Types
     *
     * Specifies the group_types that will have access
     *
     * @param array
     * @return object
     */
    public function group_types($types)
    {
        if ( ! is_array($types))
        {
            $types = (array) $types;
        }

        $this->group_types = $types;

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Unauthenticated Redirect
     *
     * Sets where to redirect when an unauthenticated user attempts access an authenticated page
     *
     * @param string
     * @return object
     */
    function unauthenticated_redirect($unauthenticated_redirect = null)
    {
        if ( ! empty($unauthenticated_redirect)) 
        {
            $this->unauthenticated_redirect = $unauthenticated_redirect;
        }
        else
        {
            $this->unauthenticated_redirect = $this->CI->config->item('unauthenticated_redirect');
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Unpermitted Redirect
     *
     * Sets where to redirect when an authenticated user attempts to
     * access a page where they do not have the correct permissions
     *
     * @param string
     * @return object
     */
    function unpermitted_redirect($unpermitted_redirect)
    {
        $this->unpermitted_redirect = $unpermitted_redirect;

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Reset
     *
     * Resets the current object to the default settings
     *
     * @return void
     */
    function reset()
    {
        $this->groups = array();
        $this->group_types = array();
        $this->method_exceptions = array();
        $this->unpermitted_redirect = null;
        $this->unauthenticated_redirect = null;
        $this->unauthenticated_redirect();
    }

    // --------------------------------------------------------------------

    /*
     * Get User Session
     *
     * Returns the current user's session object
     *
     * @return object
     */
    function get_user_session()
    {
        return  $this->CI->session->userdata('user_session');
    }

    // --------------------------------------------------------------------

    /*
     * Get Group Session
     *
     * Returns the current user's group session object
     *
     * @return object
     */
    function get_group_session()
    {
        return  $this->CI->session->userdata('group_session');
    }
}
