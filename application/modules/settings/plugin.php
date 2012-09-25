<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings_plugin extends Plugin
{
    public function site_name()
    {
        $CI =& get_instance();
        return $CI->settings->site_name;
    }
}

