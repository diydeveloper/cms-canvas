<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * @class Security is used to check if a session is initiated 
**/
class Secure
{
    public $CI;
    private $groups = array();
    private $group_types = array();
    private $method_exceptions = array();
    private $unpermitted_redirect = null;
    private $unauthenticated_redirect;

    function __construct()
    {
        $this->CI =& get_instance(); 
        $this->unauthenticated_redirect();
    }

    /**
    * checks to see if the user is permitted and if not redirects them to the location set in unauthenticated_redirect
     */
    function require_auth()
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


    // Checks that the user is authenticated had has permission
    function is_auth()
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

        return false;
    }

    /*
     * Specifies the groups that will have access
     */
    function groups($groups)
    {
        if ( ! is_array($groups))
        {
            $groups = (array) $groups;
        }

        $this->groups = $groups;

        return $this;
    }

    /*
     * Specifies the group_types that will have access
     */
    function group_types($types)
    {
        if ( ! is_array($types))
        {
            $types = (array) $types;
        }

        $this->group_types = $types;

        return $this;
    }

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

    function unpermitted_redirect($unpermitted_redirect)
    {
        $this->unpermitted_redirect = $unpermitted_redirect;

        return $this;
    }

    function reset()
    {
        $this->groups = array();
        $this->group_types = array();
        $this->method_exceptions = array();
        $this->unpermitted_redirect = null;
        $this->unauthenticated_redirect = null;
        $this->unauthenticated_redirect();
    }

    /**
    * returns the current user's session object
    **/
    function get_user_session()
    {
        return  $this->CI->session->userdata('user_session');
    }

    /**
    * returns the current user's group session object
    **/
    function get_group_session()
    {
        return  $this->CI->session->userdata('group_session');
    }
}
