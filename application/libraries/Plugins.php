<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 		PyroCMS
 * @subpackage 		Libraries
 * @author			Phil Sturgeon - PyroCMS Development Team
 *
 * Central library for Plugin logic
 */
abstract class Plugin
{
	private $attributes = array();
	private $content = '';
	private $path = '';

	// ------------------------------------------------------------------------

    function __get($var)
    {
		return get_instance()->$var;
    }

	// ------------------------------------------------------------------------

	/**
	 * content
	 *
	 * Returns content passed from the tag parser
	 *
	 * @return 	string
	 */
	public function content()
	{
		return preg_replace('/\s+$/', ' ', $this->content);
	}

	// ------------------------------------------------------------------------

	/**
	 * attributes
	 *
	 * Returns the array of attributes
	 *
	 * @return 	array
	 */
	public function attributes()
	{
		return $this->attributes;
	}

	// ------------------------------------------------------------------------

	/**
	 * attribute
	 *
	 * Returns a set attribute otherwise returns the passed default param
	 *
	 * @param	array - Params passed from view
	 * @param	array - Array of default params
	 * @return 	array
	 */
	public function attribute($param, $default = NULL)
	{
		return isset($this->attributes[$param]) ? $this->attributes[$param] : $default;
	}

	// ------------------------------------------------------------------------

	/**
	 * Set plugin path
	 *
	 * Sets content and attributes passed from Simpletags
	 *
	 * @param	array - Params passed from Plugin library process
	 * @return 	none
	 */
	public function set_path($path)
	{
        $this->path = dirname($path);
	}

	// ------------------------------------------------------------------------

	/**
	 * Set data
	 *
	 * Sets content and attributes passed from Simpletags
	 *
	 * @param	array - Params passed from Plugin library process
	 * @return 	none
	 */
	public function set_data($content, $attributes)
	{
		$content AND $this->content = $content;
		$attributes AND $this->attributes = $attributes;
	}
}

class Plugins
{
	private $loaded = array();

	function __construct()
	{
		$this->_ci = & get_instance();
	}

	function locate($plugin, $attributes, $content, $data)
	{
        if (strpos($plugin, ':') === FALSE)
        {
            // Special Case Interceptions
            // Check if user was attempting to format a date
            if (array_key_exists('format', $attributes) && isset($data[$plugin]) 
                && preg_match('/^\d{4}-\d{2}-\d{2}(\s\d{2}:\d{2}:\d{2})?$/', $data[$plugin]))
            {
                $attributes['date'] = $data[$plugin];
                $plugin = 'helper:date';
            }
            // Check if user was trying to cache an image
            else if ((array_key_exists('width', $attributes) || array_key_exists('width', $attributes))
                && isset($data[$plugin])
                && in_array(strtolower(pathinfo($data[$plugin], PATHINFO_EXTENSION)), array('gif', 'jpg', 'png', 'jpeg')))
            {
                $attributes['image'] = $data[$plugin];
                $plugin = 'helper:image_thumb';
            }
            else
            {
                return FALSE;
            }
        }

        // Setup our paths from the data array
        list($class, $method) = explode(':', $plugin);

        // Maybe plugin is a single file under the plugins directory
        if (file_exists($path = APPPATH.'plugins/'.$class.EXT))
        {
            return $this->_process($path, $class, $method, $attributes, $content, $data);
        }

        // Maybe it's a module. Plugins used as a module would typically have views or an admin interface
        if (file_exists($path = APPPATH.'modules/'.$class.'/plugin.php'))
        {
            $dirname = dirname($path).'/';

            // Set the module as a package so I can load stuff
            $this->_ci->load->add_package_path($dirname);

            $response = $this->_process($path, $class, $method, $attributes, $content, $data);

            $this->_ci->load->remove_package_path($dirname);

            return $response;
        }

		log_message('error', 'Unable to load: '.$class);

		return '';
	}

	 // --------------------------------------------------------------------

	/**
	 * Process
	 *
	 * Just process the class
	 *
	 * @access	private
	 * @param	object
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	private function _process($path, $class, $method, $attributes, $content, $data)
	{
		$class = strtolower($class);
		$class_name = ucfirst($class) . '_plugin';

		if ( ! isset($this->loaded[$class]))
		{
			include $path;
			$this->loaded[$class] = TRUE;
		}

        if ( ! class_exists($class_name))
        {
            log_message('error', 'Plugin class "' . $class_name . '" does not exist.');

            return FALSE;
        }

		$class_init = new $class_name;
		$class_init->set_path($path);
		$class_init->set_data($content, $attributes);

        if ( ! is_callable(array($class_init, $method)))
        {
            // But does a property exist by that name?
            if (property_exists($class_init, $method))
            {
                return TRUE;
            }

            log_message('error', 'Plugin method "' . $method . '" does not exist on class "' . $class_name . '".');

            return FALSE;
        }

        return call_user_func(array($class_init, $method), $data);
	}
}
