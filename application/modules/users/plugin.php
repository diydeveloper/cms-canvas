<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Users_plugin extends Plugin
{
    public function is_logged_in()
    {
        $CI =& get_instance();
        return $CI->secure->is_auth();
    }

    public function is_admin()
    {
        $CI =& get_instance();
        return $CI->secure->group_types(array(ADMINISTRATOR))->is_auth();
    }

    public function group()
    {
        $CI =& get_instance();

        $Group = $CI->session->userdata('group_session');

        if (is_object($Group))
        {
            return $Group->name;
        }

        return '';
    }

    public function first_name()
    {
        $CI =& get_instance();

        $User = $CI->session->userdata('user_session');

        if (is_object($User))
        {
            return $User->first_name;
        }

        return '';
    }

    public function last_name()
    {
        $CI =& get_instance();

        $User = $CI->session->userdata('user_session');

        if (is_object($User))
        {
            return $User->last_name;
        }

        return '';
    }
}

