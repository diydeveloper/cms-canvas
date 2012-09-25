<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Admin is Permitted
 *
 * Checks if current uri segments exist in array of uri strings
 *
 * @param string
 * @return bool
 */
if ( ! function_exists('admin_is_permitted'))
{
    function admin_is_permitted($uri)
    {
        $CI =& get_instance();
        $permissions = unserialize($CI->Group_session->permissions);
        $permissions = isset($permissions['access']) ? $permissions['access'] : array();
        $access_options = unserialize(ADMIN_ACCESS_OPTIONS);

        if (( ! in_uri($permissions, $uri) && $CI->Group_session->type == ADMINISTRATOR)  && in_uri($access_options, $uri, TRUE))
        {
            return false;
        }

        return true;
    }   
}   

// ------------------------------------------------------------------------

/*
 * Set Filter
 *
 * Retrieves the value of a specific filter field from
 * them filter session variable array
 *
 * @param string
 * @param string
 * @return string
 */
if ( ! function_exists('set_filter'))
{
    function set_filter($filter_type, $filter)
    {
        $CI =& get_instance();

        $filter_array = $CI->session->userdata('filter[\'' . $filter_type . '\']');

        if (isset($filter_array[$filter]))
        {
            return $filter_array[$filter];
        }
        else
        {
            return '';
        }
    }
}

// ------------------------------------------------------------------------

/*
 * Process Filter
 *
 * Sets, Gets, and Clears filter session data 
 * for various filters in the admin panel
 *
 * @param string
 * @return array
 */
if ( ! function_exists('process_filter'))
{
    function process_filter($filter_type)
    {
        $CI =& get_instance();

        // Process Filter
        if ($CI->input->post('clear_filter'))
        {
            $CI->session->unset_userdata('filter[\'' . $filter_type . '\']');
            redirect(current_url());
        }
        else
        {
            if ($filter = $CI->input->post('filter'))
            {
                foreach($filter as $key => $value)
                {
                    if ($value == '')
                    {
                        unset($filter[$key]);
                    }
                }

                $CI->session->set_userdata('filter[\'' . $filter_type . '\']', $filter);
                redirect(current_url());
            }
        }

        $filter = ($CI->session->userdata('filter[\'' . $filter_type . '\']')) ? $CI->session->userdata('filter[\'' . $filter_type . '\']') : array();

        return $filter;
    }
}

// ------------------------------------------------------------------------

/*
 * Set Crumbs
 *
 * Generates the html to build the admin panels breadcrumbs 
 *
 * @param array
 * @return string
 */
if ( ! function_exists('set_crumbs'))
{
    function set_crumbs($crumbs)
    {
        if (current_url() == site_url(ADMIN_PATH) || current_url() == site_url(ADMIN_PATH . '/dashboard'))
        {
            $crumb_str = '<span class="first_crumb current">Dashboard</span>';
        }
        else
        {
            $crumb_str = '<a class="first_crumb" href="' . site_url(ADMIN_PATH) . '">Dashboard</a>';
        }

        foreach($crumbs as $uri => $crumb)
        {
            if ($uri == current_url())
            {
                $crumb_str .= '<span class="current">' . $crumb . '</span>';
            }
            else
            {
                $crumb_str .= '<a href="' . site_url(ADMIN_PATH . '/' . trim($uri, '/')) . '">' . $crumb . '</a>';
            }
        }

        return $crumb_str;
    }
}

// ------------------------------------------------------------------------

/*
 * Admin Nav
 *
 * Generates the html to build the admin navigation 
 * showing only links that the admin is permitted
 *
 * @param array
 * @param int
 * @return string
 */
if ( ! function_exists('admin_nav'))
{
    function admin_nav($nav, $depth = 1)
    {
        $list_item = '<ul>';

        foreach($nav as $item)
        {
            $item['url'] = trim($item['url'], '/');

            if (show_admin_nav_li($item) && empty($item['hidden']))
            {
                $list_item .= '<li' . (($depth == 1 && is_admin_nav_li_selected($item)) ? ' class="selected"' : '') . ((isset($item['id'])) ? ' id="' . $item['id'] . '"' : '') . '>';
                $list_item .= '<a href="' . (admin_is_permitted(ADMIN_PATH . '/' . $item['url']) ? site_url(ADMIN_PATH . '/' . $item['url']) : 'javascript:void(0)') . '"' . (($depth == 1) ? ' class="top"' : '') . '>' . $item['title'] . '</a>';

                if ( ! empty($item['sub']))
                {
                    $list_item .= admin_nav($item['sub'], $depth + 1);
                }

                $list_item .= '</li>';
            }
        }

        $list_item .= '</ul>';

        return $list_item;
    }   
}   

// ------------------------------------------------------------------------

/*
 * Show Admin Nav Li
 *
 * Called by admin_nav  to determine is admin is permitted to nav item
 *
 * @param array
 * @param int
 * @return bool
 */
if ( ! function_exists('show_admin_nav_li'))
{
    function show_admin_nav_li($item, $depth = 1)
    {
        if (admin_is_permitted(ADMIN_PATH . '/' . trim($item['url'], '/')) && empty($item['hidden']))
        {
            return TRUE;
        }

        if ( ! empty($item['sub']) && empty($item['hidden']))
        {
            foreach ($item['sub'] as $item)
            {
                if (show_admin_nav_li($item, $depth + 1))
                {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }   
}   

// ------------------------------------------------------------------------

/*
 * Is Admin Nav Li Selected
 *
 * Called by admin_nav to determine if current page 
 * is in the current trail of current nav item
 *
 * @param array
 * @param int
 * @return bool
 */
if ( ! function_exists('is_admin_nav_li_selected'))
{
    function is_admin_nav_li_selected($item, $depth = 1)
    {
        $CI =& get_instance();

        $uri_segments = explode('/', $item['url']);
        $segment_match = TRUE;

        $i = 2;
        foreach($uri_segments as $segment)
        {
            if ($CI->uri->segment($i) != $segment)
            {
                $segment_match = FALSE;
                break;
            }

            $i++;
        }

        if ($segment_match)
        {
            return TRUE;
        }

        if ( ! empty($item['sub']))
        {
            foreach ($item['sub'] as $item)
            {
                if (is_admin_nav_li_selected($item, $depth + 1))
                {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }   
}   
