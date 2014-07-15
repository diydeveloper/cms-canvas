<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Theme_editor_model extends CI_Model
{	
    function get_theme_files($extension = 'php', $directory = '/')
    {
        $theme_dir = CMS_ROOT . 'themes/' . $this->settings->theme . '/';
        $pattern = $theme_dir . trim($directory, '/') . '/*.' . $extension;

        $file_array = array();
        $files = glob_recursive($pattern, GLOB_BRACE);
        if($files)
            foreach ($files as $file)
            {
                $relative_path = str_replace(dirname($pattern) . '/', '', $file);
                $theme_path = str_replace($theme_dir, '', $file);
    
                $file_array[$relative_path] = array(
                    'hash'          => url_base64_encode($theme_path), // relative to the current theme's root directory
                    'theme_path'    => $theme_path,
                    'relative_path' => $relative_path,
                    'title'         => ucwords(str_replace(array('_', '-'), ' ', basename($file, '.' . $extension))),
                );
            }

        return $file_array;
    }
}
