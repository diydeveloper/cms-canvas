<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Cache 
{
    // Cache Directories
    private $cms_cache = 'assets/cms/cache/';

    function __construct() 
    {
        $this->CI = get_instance();
        $this->CI->load->helper('file');
    }

    // ------------------------------------------------------------------------

    /*
     * Model
     *
     * Executes and caches returned data from a model method
     *
     * @param string
     * @param string
     * @param array
     * @return mixed
     */
    function model($model, $method, $arguments = array(), $dir='')
    {               
        $this->CI->load->add_package_path(APPPATH.'modules/content/');
        $this->CI->load->model($model);      

        if ( ! is_array($arguments))
        {
            $arguments = (array) $arguments;
        }

        $cache_id = sha1($method.serialize($arguments));

        // Remove _model from model name to create the cache directory
        if (empty($dir))
        {
            $dir = str_replace('_model', '', strtolower($model)) . '/';
        }

        // Read cached file if it exists
        $data = $this->get($cache_id, $dir);

        if ($data !== FALSE)
        {
            // Data was returned from cache file
            return $data;
        }
        else
        {
            // Instantiate a new instance of the model
            $Class = new $model();
            $Object = $Class->$method($arguments);

            if (is_callable(array($Object, 'exists')) && $Object->exists() || (is_array($Object) && count($Object) > 0))
            {
                $this->save($cache_id, $dir, $Object);
            }

            return $Object;
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Get
     *
     * Unserializes data from cache file
     *
     * @param string
     * @return bool
     */
    function get($id, $dir)
    {
        $cache_dir = $this->cms_cache . trim($dir, '/') . '/';

        // Read cached file if it exists
        if (file_exists(FCPATH . $cache_dir . $id . '.cache'))
        {
            $content = read_file(FCPATH . $cache_dir . $id . '.cache');

            return @unserialize($content);
        }

        return FALSE;
    }

    // ------------------------------------------------------------------------

    /*
     * Save
     *
     * Serializes and writes data to cache file
     *
     * @param string
     * @param string
     * @param string
     * @return bool
     */
    function save($id, $dir, $data)
    {
        $cache_dir = $this->cms_cache . trim($dir, '/') . '/';

        // Check if the cache directory exists
        // If not create it
        if ( ! file_exists(FCPATH . $cache_dir))
        {
            @mkdir(FCPATH . $cache_dir);
        }

        // Write data to cache file
        if ( ! write_file(FCPATH . $cache_dir . $id . '.cache', @serialize($data)))
        {
            $this->CI->session->set_flashdata('message', '<p class="error">Error compiling: ' . $cache_dir . ' is not writable.</p>');
            return FALSE;
        }

        return TRUE;
    }

    // ------------------------------------------------------------------------

    /*
     * Delete All
     *
     * This function will  delete caches files in a specified directory
     *
     * @param string
     * @return void
     */
    function delete_all($dir = '')
    {
         $this->CI->load->helper('file');

         if ( file_exists(FCPATH . $this->cms_cache . $dir) ) 
         {
             delete_files(FCPATH . $this->cms_cache . $dir, TRUE);
         }
    }
}
