<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Nav
 *
 * Outputs and caches a navigation
 *
 * @param int
 * @param array
 * @return string
 */
if ( ! function_exists('nav'))
{
    function nav($nav_id, $config = array())
    {
        $CI =& get_instance();

        $CI->load->library('navigations/navigations_library');

        // Set the attribute id as the tag id
        if (isset($config['id']))
        {
            $config['tag_id'] = $config['id'];
            unset($config['id']);
        }

        // Set the id as the config attribute id;
        $config['id'] = $nav_id;

        return $CI->navigations_library->list_nav($config);
    }
}

// ------------------------------------------------------------------------

/*
 * Breadcrumb
 *
 * Builds a breadcrumb unordered list
 *
 * @param int
 * @param array
 * @return string
 */
if ( ! function_exists('breadcrumb'))
{
    function breadcrumb($id, $config = array())
    {
        $CI =& get_instance();

        $CI->load->library('navigations/navigations_library');

        // Set the attribute id as the tag id
        if (isset($config['id']))
        {
            $config['tag_id'] = $config['id'];
            unset($config['id']);
        }

        // Set the id as the config attribute id;
        $config['id'] = $id;

        return $CI->navigations_library->breadcrumb($config);
    }
}
