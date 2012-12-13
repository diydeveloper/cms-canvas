<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Print Recursive
 *
 * Simply wraps a print_r() in pre tags for debugging.
 *
 * @param mixed
 * @return string
 */
if ( ! function_exists('_pr'))
{
    function _pr($a)
    {
        echo "<pre>";
        print_r($a);
        echo "</pre>";
    }
}

// ------------------------------------------------------------------------

/*
 * Variable Dump
 *
 * Simply wraps a var_dump() in pre tags for debugging.
 *
 * @param mixed
 * @return string
 */
if ( ! function_exists('_vd'))
{
    function _vd($a)
    {
        echo "<pre>";
        var_dump($a);
        echo "</pre>";
    }
}

// ------------------------------------------------------------------------

/*
 * Array to Object
 * 
 * Converts an array to an object
 *
 * @param array
 * @return object
 */
if ( ! function_exists('array_to_object'))
{
    function array_to_object($array)
    {
        $Object = new stdClass();
        foreach($array as $key=>$value)
        {
            $Object->$key = $value; 
        }

        return $Object;
    }
}

// ------------------------------------------------------------------------

/*
 * Object to Array
 * 
 * Converts an object to an array
 * 
 * @param object
 * @return array
 */
if ( ! function_exists('object_to_array'))
{
    function object_to_array($Object)
    {
        $array = get_object_vars($Object);

        return $array;
    }
}

// ------------------------------------------------------------------------

/*
 * Is Ajax
 *
 * Returns true if request is ajax protocol
 *
 * @return bool
 */
if ( ! function_exists('is_ajax'))
{
    function is_ajax() 
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }
}

// ------------------------------------------------------------------------

/*
 * Image Thumb
 *
 * Creates an image thumbnail and caches the image
 *
 * @param string
 * @param int
 * @param int
 * @param bool
 * @param array
 * @return string
 */
if ( ! function_exists('image_thumb'))
{
    function image_thumb($source_image, $width = 0, $height = 0, $crop = FALSE, $props = array()) 
    {
        $CI =& get_instance();
        $CI->load->library('image_cache');

        $props['source_image'] = $source_image;
        $props['width'] = $width;
        $props['height'] = $height;
        $props['crop'] = $crop;

        $CI->image_cache->initialize($props);
        $image = $CI->image_cache->image_cache();
        $CI->image_cache->clear();

        return $image;
    }
}

// ------------------------------------------------------------------------

/*
 * BR 2 NL
 *
 * Converts html <br /> to new line \n
 *
 * @param string
 * @return string
 */
if ( ! function_exists('br2nl'))
{
    function br2nl($text)                                                                   
    {                                                                                       
        return  preg_replace('/<br\\s*?\/??>/i', '', $text);                            
    }                                                                                       
}

// ------------------------------------------------------------------------

/* 
 * Option Array Value
 *
 * Returns single dimension array from an Array of objects with the key and value defined
 *
 * @param array
 * @param string
 * @param string
 * @param array
 * @return array
 */
if ( ! function_exists('option_array_value'))
{
    function option_array_value($object_array, $key, $value, $default = array())
    {
        $option_array = array();

        if ( ! empty($default))
        {
            $option_array = $default;
        }

        foreach($object_array as $Object)
        {
            $option_array[$Object->$key] = $Object->$value;
        }

        return $option_array;
    }
}

// ------------------------------------------------------------------------

/* 
 * In URI
 *
 * Checks if current uri segments exist in array of uri strings
 *
 * @param string or array
 * @param string
 * @param bool
 * @return bool
 */
if ( ! function_exists('in_uri'))
{
    function in_uri($uri_array, $uri = null, $array_keys = FALSE)
    {
        if ( ! is_array($uri_array)) 
        {
            $uri_array = array($segments);
        }

        $CI =& get_instance();

        if ( ! empty($uri))
        {
            $uri_string = '/' . trim($uri, '/');
        }
        else
        {
            $uri_string = '/' . trim($CI->uri->uri_string(), '/');
        }

        $uri_array = ($array_keys) ? array_keys($uri_array) : $uri_array;

        foreach($uri_array as $string)
        {
            if (strpos($uri_string, ($string != '') ? '/' . trim($string, '/') : ' ') === 0)
            {
                return true;
            }
        }

        return false;
    }   
}   

// ------------------------------------------------------------------------

/* 
 * Theme Partial
 *
 * Load a theme partial
 *
 * @param string
 * @param array
 * @param bool
 * @return string
 */
if ( ! function_exists('theme_partial'))
{
    function theme_partial($view, $vars = array(), $return = TRUE)
    {
        $CI =& get_instance();
        $CI->load->library('parser');
        return $CI->parser->parse_string($CI->load->theme($CI->template->theme . '/views/partials/' . trim($view, '/'), $vars, $return, $CI->template->theme_path), $vars, $return);
    }
}

// ------------------------------------------------------------------------

/* 
 * Theme Url
 *
 * Create a url to the current theme
 *
 * @param string
 * @return string
 */
if ( ! function_exists('theme_url'))
{
    function theme_url($uri = '')
    {
        $CI =& get_instance();
        return site_url($CI->template->theme_path . '/' . $CI->template->theme . '/'  . trim($uri, '/'));
    }
}


// ------------------------------------------------------------------------

/* 
 * Domain Name
 *
 * Returns the site domain name and tld
 *
 * @return string
 */
if ( ! function_exists('domain_name'))
{
    function domain_name()
    {
        $CI =& get_instance();

        $info = parse_url($CI->config->item('base_url'));
        $host = $info['host'];
        $host_names = explode(".", $host);
        return $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
    }
}

// ------------------------------------------------------------------------

/*
 * Glob Recursive
 *
 * Run glob function recursivley on a directory
 *
 * @param string
 * @return array
 */
if ( ! function_exists('glob_recursive'))
{
    // Does not support flag GLOB_BRACE
    
    function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
        
        return $files;
    }
}

// ------------------------------------------------------------------------

/*
 * URL Base64 Encode
 * 
 * Encodes a string as base64, and sanitizes it for use in a CI URI.
 * 
 * @param string
 * @return string
 */
if ( ! function_exists('url_base64_encode'))
{
    function url_base64_encode(&$str="")
    {
        return strtr(
            base64_encode($str),
            array(
                '+' => '.',
                '=' => '-',
                '/' => '~'
            )
        );
    }
}

// ------------------------------------------------------------------------

/*
 * URL Base64 Decode
 *
 * Decodes a base64 string that was encoded by ci_base64_encode.
 * 
 * @param string
 * @return string
 */
if ( ! function_exists('url_base64_decode'))
{
    function url_base64_decode(&$str="")
    {
        return base64_decode(strtr(
            $str, 
            array(
                '.' => '+',
                '-' => '=',
                '~' => '/'
            )
        ));
    }
}

// ------------------------------------------------------------------------

/*
 * Output XML
 *
 * Sets the header content type to XML and
 * outputs the <?php xml tag
 * 
 * @param string
 * @return string
 */
if ( ! function_exists('xml_output'))
{
    function xml_output()
    {
        $CI =& get_instance();
        $CI->output->set_content_type('text/xml');
        $CI->output->set_output("<?xml version=\"1.0\"?>\r\n");
    }
}

// ------------------------------------------------------------------------

/*
 * JS Head Start
 *
 * Starts output buffering to place javascript in the <head> of the template
 * 
 * @return void
 */
if ( ! function_exists('js_start'))
{
    function js_start()
    {
        ob_start();
    }
}

// ------------------------------------------------------------------------

/*
 * JS Head End
 *
 * Ends output buffering to place javascript in the <head> of the template
 * 
 * @return void
 */
if ( ! function_exists('js_end'))
{
    function js_end()
    {
        $CI =& get_instance();
        $CI->template->add_script(ob_get_contents());
        ob_end_clean();
    }
}

// ------------------------------------------------------------------------

/*
 * String to Boolean
 *
 * This function analyzes a string and returns false if the string is empty, false, or 0
 * and true for everything else
 * 
 * @param string
 * @return bool
 */
if ( ! function_exists('str_to_bool'))
{
    function str_to_bool($str)
    {
        if (is_bool($str))
        {
            return $str;
        }

        $str = (string) $str;
        
        if (in_array(strtolower($str), array('false', '0', '')))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}