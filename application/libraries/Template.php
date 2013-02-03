<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Template 
{
    /*
     * Declared class variables
     */
    public $CI;
    public $parse_views = FALSE;
    public $headers_sent = FALSE; // Checks if HTML <head> data has been outputted
    public $page_head = '';
    public $template_data = array();
    public $javascripts = array();
    public $scripts = array();
    public $stylesheets = array();
    public $css = array();
    public $theme;
    public $layout;
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $theme_path = 'themes';

    private $_js_order = array();  // Tracks the order in which javascripts and inline scripts were added
    private $_css_order = array();  // Tracks the order in which stylesheets and inline css were added

    function __construct()
    {
        $this->CI =& get_instance();
        $this->set_theme();

        $this->parse_views = $this->CI->config->item('parse_views');
    }

    // --------------------------------------------------------------------
    
    /*
     * Set
     *
     * Used to set data used in the template
     *
     * @param string 
     * @return object
     */
    function set($name, $value)
    {
        $this->template_data[$name] = $value;

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Set Theme
     *
     * Specifies a theme to use other than the default
     *
     * @param string 
     * @param string 
     * @param string 
     * @return object
     */
    function set_theme($theme = null, $layout = null, $theme_path = 'themes')
    {
        if ( ! empty($theme))
        {
            $this->theme = $theme;
        }
        else
        {
            $this->theme = $this->CI->settings->theme;
        }

        // Set theme layout
        $this->set_layout($layout);

        // Set theme path
        $this->theme_path = $theme_path;

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Set Layout
     *
     * Specifies a layout to use other than the default
     *
     * @param string 
     * @return object
     */
    function set_layout($layout = null)
    {
        if (is_null($layout))
        {
            $this->layout = $this->CI->settings->layout;
        }
        else if ($layout != '')
        {
            $this->layout = $layout;
        }
        else
        {
            $this->layout = '';
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Set Title
     *
     * Specifies the page title used in the metadata output
     *
     * @param string 
     * @return object
     */
    function set_meta_title($title)
    {
        if ( ! empty($title))
        {
            $this->meta_title = $title;
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Set Description
     *
     * Specifies the page description used in the metadata output
     *
     * @param string 
     * @return object
     */
    function set_meta_description($description)
    {
        if ( ! empty($description))
        {
            $this->meta_description = $description;
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Set Keywords
     *
     * Specifies the page keywords used in the metadata output
     *
     * @param string 
     * @return object
     */
    function set_meta_keywords($keywords)
    {
        if ( ! empty($keywords))
        {
            $this->meta_keywords = $keywords;
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * View
     *
     * Loads a specified view wrapped in the default theme
     *
     * @param string 
     * @param array
     * @param bool
     * @return string
     */
    function view($view = '', $view_data = array(), $return = FALSE)
    {               
        if ($this->parse_views)
        {
            $this->template_data = array_merge($view_data, $this->template_data);

            $this->CI->load->library('parser');
            $this->CI->benchmark->mark('template_parser_start');

            if ($this->layout == '')
            {
                // No theme layout selected, parse and output content type
                return $this->CI->parser->parse($view, $this->template_data);
            }
            else
            {
                $this->set('content', $this->CI->parser->parse($view, $view_data, TRUE));
                $view = $this->CI->parser->parse_string($this->CI->load->theme($this->theme."/views/layouts/" . $this->layout, $this->template_data, TRUE, $this->theme_path), $this->template_data, $return, TRUE);
            }

            $this->CI->benchmark->mark('template_parser_end');

            return $view;
        }
        else
        {
            if ($this->layout == '')
            {
                // No theme layout selected, output content type
                return $this->CI->load->view($view, $this->template_data);
            }
            else
            {
                $this->set('content', $this->CI->load->view($view, $view_data, TRUE));
                return $this->CI->load->theme($this->theme."/views/layouts/" . $this->layout, $this->template_data, $return, $this->theme_path);
            }
        }
    }

    // --------------------------------------------------------------------

    /*
     * Add Stylesheet
     *
     * This function is used to build an array of external stylesheets to include
     *
     * @param string or array
     * @return object
     */
    function add_stylesheet($stylesheets)
    {
        if ( ! is_array($stylesheets))
        {
            $stylesheets = (array) $stylesheets;
        }

        foreach ($stylesheets as $stylesheet)
        {
            $stylesheet = (strpos($stylesheet, 'http') === 0 ? $stylesheet : base_url($stylesheet));

            if ( ! in_array($stylesheet, $this->stylesheets))
            {
                $this->stylesheets[] = $stylesheet;
                $index = end(array_keys($this->stylesheets));

                // Keep track of the order in which stylesheets and css are added
                $this->_css_order[] = array(
                        'array' => 'stylesheets',
                        'index' => $index,
                    );
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Add CSS
     *
     * Used to build an array of internal css to include
     *
     * @param string or array
     * @return object
     */
    function add_css($css)
    {
        if ( ! is_array($css))
        {
            $css = (array) $css;
        }

        foreach ($css as $style)
        {
            $this->css[] = $style;
            $index = end(array_keys($this->css));

            // Keep track of the order in which stylesheets and css are added
            $this->_css_order[] = array(
                    'array' => 'css',
                    'index' => $index,
                );
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Add Javascript
     *
     * Used to build an array of external javascripts to include
     *
     * @param string or array
     * @return object
     */
    function add_javascript($javascripts)
    {
        if ( ! is_array($javascripts))
        {
            $javascripts = (array) $javascripts;
        }

        foreach ($javascripts as $javascript)
        {
            // If HTTP not in javascript uri add prepend base_url
            $javascript = (strpos($javascript, 'http') === 0 ? $javascript : base_url($javascript));

            if ( ! in_array($javascript, $this->javascripts))
            {
                $this->javascripts[] = $javascript;
                $index = end(array_keys($this->javascripts));

                // Keep track of the order in which javascripts and scripts are added
                $this->_js_order[] = array(
                        'array' => 'javascripts',
                        'index' => $index,
                    );
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Add Script
     *
     * Used to include internal javascript in the template
     *
     * @param string or array
     * @return object
     */
    function add_script($scripts)
    {
        if ( ! is_array($scripts))
        {
            $scripts = (array) $scripts;
        }

        foreach ($scripts as $javascript)
        {
            $this->scripts[] = $javascript;
            $index = end(array_keys($this->scripts));

            // Keep track of the order in which javascripts and scripts are added
            $this->_js_order[] = array(
                    'array' => 'scripts',
                    'index' => $index,
                );
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Add Page Head
     *
     * Used to include custom JavaScript, CSS, meta information and/or PHP in the <head> block of the template
     *
     * @param string
     * @return object
     */
    function add_page_head($code)
    {
        if (is_string($code))
        {
            $this->page_head = $code;
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Add Package
     *
     * Used to add predefined sets of javascripts and stylesheets
     *
     * @param string or array
     * @return object
     */
    function add_package($packages)
    {
        $pkg_const = unserialize(PACKAGES);

        if ( ! is_array($packages))
        {
            $packages = (array) $packages;
        }

        foreach ($packages as $package)
        {
            if (isset($pkg_const[$package]))
            {
                $package = $pkg_const[$package];

                if (isset($package['javascript']))
                {
                    $this->add_javascript($package['javascript']);
                }

                if (isset($package['stylesheet']))
                {
                    $this->add_stylesheet($package['stylesheet']);
                }
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------

    /*
     * Metadata
     *
     * Commonly used in the header.php template file
     * Outputs title, description, and keyword metadata
     *
     * @return string
     */
    function metadata()
    {
        $metadata = '';

        if ( ! empty($this->meta_title))
        {
            $metadata .= '<title>' . $this->meta_title . '</title>' . "\r\n";
        }

        if ( ! empty($this->meta_description))
        {
            $metadata .= '<meta name="description" content="' . $this->meta_description . '" />' . "\r\n";
        }

        if ( ! empty($this->meta_keywords))
        {
            $metadata .= '<meta name="keywords" content="' . $this->meta_keywords . '" />' . "\r\n";
        }

        $this->headers_sent = TRUE;

        return $metadata;
    }

    // --------------------------------------------------------------------

    /*
     * Javascripts
     *
     * Commonly used in the HTML <head> of template files
     * Outputs javascript includes from the javascript array
     *
     * @return string
     */
    function javascripts()
    {
        $js_includes = "\n\t<script>var BASE_HREF=\"" . base_url() . "\"</script>";

        foreach ($this->_js_order as $js_order) 
        {
            if ($js_order['array'] == 'javascripts')
            {
                $js_includes .=  "\n\t<script type=\"text/javascript\" src=\"" . $this->javascripts[$js_order['index']] . "\"></script>";
            }
            else if ($js_order['array'] == 'scripts')
            {
                $script = $this->scripts[$js_order['index']];

                // Check if script has the script tags included
                if (stripos(trim($script), '<script') === 0)
                {
                    $js_includes .=  "\n" . $script;
                }
                else
                {
                    $js_includes .=  "\n\t<script type=\"text/javascript\">" . $script . "</script>";
                }
            }
        }

        $this->headers_sent = TRUE;

        return $js_includes;
    }

    // --------------------------------------------------------------------

    /*
     * Stylesheets
     *
     * Commonly used in the HTML <head> of template files
     * Outputs stylesheets includes from the stylesheet array
     *
     * @return string
     */
    function stylesheets()
    {
        $css_includes = '';

        foreach ($this->_css_order as $css_order) 
        {
            if ($css_order['array'] == 'stylesheets')
            {
                $css_includes .=  "\n\t<link href=\"" . $this->stylesheets[$css_order['index']] . "\" rel=\"stylesheet\" type=\"text/css\" />";
            }
            else if ($css_order['array'] == 'css')
            {
                $style = $this->css[$css_order['index']];

                // Check if css has the script tags included
                if (stripos(trim($style), '<stle') === 0)
                {
                    $css_includes .=  "\n" . $style;
                }
                else
                {
                    $css_includes .=  "\n\t<style type=\"text/css\">" . $style . "</style>";
                }
            }
        }

        $this->headers_sent = TRUE;

        return $css_includes;
    }

    // --------------------------------------------------------------------

    /*
     * Analytics
     *
     * Commonly used in the header template file immediately before the closing </head> tag
     * Outputs javascript for google analytics
     * Google Analytic's Account ID is set in /application/config/custom_config.php
     *
     * @return string
     */
    function analytics()
    {
        if ($this->CI->settings->ga_account_id)
        {
            return "<script type=\"text/javascript\">
     
                    var _gaq = _gaq || [];
                    _gaq.push(['_setAccount', '" . $this->CI->settings->ga_account_id . "']);
                    _gaq.push(['_trackPageview']);
     
                    (function() {
                      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                    })();
     
                  </script>";
        }
    }

    // --------------------------------------------------------------------

    /*
     * Page Head
     *
     * Commonly used in the HTML <head> of template files
     * Outputs additional page <head> code
     *
     * @return string
     */
    function page_head()
    {
        $this->headers_sent = TRUE;

        return $this->page_head;
    }

    // --------------------------------------------------------------------

    /*
     * Head
     *
     * Commonly used in the header template file immediately after the starting <head> tag
     * Combines the outputs of metadata, stylesheets, javascripts, and analytics in one function
     *
     * @return string
     */
    function head()
    {
        $return = '';
        $return .= $this->metadata();
        $return .= $this->stylesheets();
        $return .= $this->javascripts();
        $return .= $this->page_head();
        $return .= $this->analytics();

        return $return;
    }

    // --------------------------------------------------------------------

    /*
     * Get Themes
     *
     * Returns all themes located in the themes folder in the root directory
     *
     * @return array
     */
    function get_themes()
    {
        $this->CI->load->helper('file');
        $themes_array = array();

        $themes = glob(CMS_ROOT . 'themes/*', GLOB_ONLYDIR);

        if (is_array($themes))
        {
            foreach($themes as $theme)
            {
                $theme_name = basename($theme);
                $themes_array[$theme_name] = ucwords(str_replace(array('-', '_'), ' ', $theme_name));
            }
        }

        return $themes_array;
    }

    // --------------------------------------------------------------------

    /*
     * Get Theme Layouts
     *
     * Returns all layouts recursively located in the specified theme folder
     *
     * @param string
     * @return array
     */
    function get_theme_layouts($theme = '', $add_default = FALSE)
    {
        $this->CI->load->helper('file');
        $layouts_array = array();

        $theme = ( ! empty($theme)) ? $theme : $this->CI->settings->theme;

        if ($add_default)
        {
            $default_layout = $this->CI->settings->layout;
            $layouts_array = array(
                $default_layout => $default_layout . ' (*Default*)',
            );
        }

        $layout_files = get_dir_file_info(CMS_ROOT . 'themes/' . $theme . '/views/layouts/', FALSE);

        if (is_array($layout_files))
        {
            foreach($layout_files as $key => $file_info)
            {
                $layout = pathinfo($file_info['name'], PATHINFO_FILENAME);

                // Incase there are any subdirectories with layouts
                $relative_path = str_replace(CMS_ROOT . 'themes/' . $theme . '/views/layouts/', '', $file_info['relative_path']);

                // Don't show the default layout so that it will automatically 
                // be selected if no layout is chosen
                if ($add_default)
                {
                    if ($layout != $default_layout)
                    {
                        $layouts_array[$relative_path . $layout] = $layout;
                    }
                }
                else
                {
                    $layouts_array[$relative_path . $layout] = $layout;
                }
            }
        }

        if ($add_default)
        {
            $layouts_array[''] = '-- None --';
        }

        return $layouts_array;
    }
}
