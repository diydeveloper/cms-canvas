<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Settings_plugin extends Plugin
{
    public function site_name()
    {
        $CI =& get_instance();
        return $CI->settings->site_name;
    }
}

