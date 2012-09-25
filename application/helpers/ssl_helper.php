<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('force_ssl'))
{
    function force_ssl()
    {
        if ($_SERVER['SERVER_PORT'] != 443)
        {
            $CI =& get_instance();
            $CI->config->config['base_url'] = str_replace('http://', 'https://', str_replace('www.', '', $CI->config->config['base_url']));
            redirect($CI->uri->uri_string()); 
        }   
    }   
}   

if ( ! function_exists('remove_ssl'))
{
    function remove_ssl()
    {
        if ($_SERVER['SERVER_PORT'] != 80)
        {
            $CI =& get_instance();
            $CI->config->config['base_url'] = str_replace('https://', 'http://', $CI->config->config['base_url']);
            redirect($CI->uri->uri_string()); 
        }   
    }   
}   
