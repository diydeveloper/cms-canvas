<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Settings 
{
    public $cache;
    public $CI;

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('cache');
        $this->get_all();
    }

    function __set($slug, $value)
    {
        $this->cache[$slug] = $value;
    }

    function __get($slug)
    {
        // If setting not defined in the database look in the config files
        if (isset($this->cache->$slug) && $this->cache->$slug != '')
        {
            return $this->cache->$slug;
        }
        else
        {
            return $this->CI->config->item($slug);
        }
    }

    function get_all()
    {
        if (is_object($this->cache) && count((array) $this->cache))
        {
            return $this->cache;
        }

        $this->cache = $this->CI->cache->get(sha1('general_settings'), 'settings');

        if ($this->cache === FALSE)
        {
            $Settings = $this->CI->db->get('settings');

            $this->cache = new stdClass();

            foreach ($Settings->result() as $Setting)
            {
                if ($Setting->module && ! isset($this->cache->{$Setting->module . '_module'}))
                {
                    $this->cache->{$Setting->module . '_module'} = new stdClass();
                    $this->cache->{$Setting->module . '_module'}->{$Setting->slug} = $Setting->value;
                }
                elseif ($Setting->module)
                {
                    $this->cache->{$Setting->module . '_module'}->{$Setting->slug} = $Setting->value;
                }
                else
                {
                    $this->cache->{$Setting->slug} = $Setting->value;
                }
            }

            $this->CI->cache->save(sha1('general_settings'), 'settings', $this->cache);
        }
    }
}
