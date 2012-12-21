<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class MY_Parser extends CI_Parser {

    private $_ci;
    private $callbacks = array();

    function __construct()
    {
        $this->_ci = get_instance();

        if ( ! class_exists('Lex_Autoloader'))
        {
            include APPPATH.'/libraries/Lex/Autoloader.php';
        }
    }

    // --------------------------------------------------------------------

    /**
     *  Parse a view file
     *
     * Parses pseudo-variables contained in the specified template,
     * replacing them with the data in the second param
     *
     * @access  public
     * @param   string
     * @param   array
     * @param   bool
     * @return  string
     */
    function parse($template, $data = array(), $return = FALSE, $inject_noparse = FALSE)
    {
        $string = $this->_ci->load->view($template, $data, TRUE);

        return $this->_parse($string, $data, $return, $inject_noparse);
    }

    // --------------------------------------------------------------------

    /**
     *  String parse
     *
     * Parses pseudo-variables contained in the string content,
     * replacing them with the data in the second param
     *
     * @access  public
     * @param   string
     * @param   array
     * @param   bool
     * @return  string
     */
    function parse_string($string, $data = array(), $return = FALSE, $inject_noparse = FALSE)
    {
        return $this->_parse($string, $data, $return, $inject_noparse);
    }

    // --------------------------------------------------------------------

     /**
      *  Parse
      *
      * Parses pseudo-variables contained in the specified template,
      * replacing them with the data in the second param
      *
      * @access  public
      * @param   string
      * @param   array
      * @param   bool
      * @return  string
      */
     function _parse($string, $data, $return = FALSE, $inject_noparse = FALSE)
     {
         // Convert from object to array
         if ( ! is_array($data))
         {
             $data = (array) $data;
         }

         // Global tags
         $data['site_url'] = trim(site_url(), '/');
         $data['theme_url'] = trim(theme_url(), '/');

         // Not sure if needed
         // ALERT THIS MAY CAUSE INFINITE LOOP
         $data = array_merge($data, $this->_ci->load->_ci_cached_vars);

         // Lex processing
         Lex_Autoloader::register();

         $parser = new Lex_Parser();
         $parser->scope_glue(':');
         $parser->cumulative_noparse(TRUE);
         $parsed = $parser->parse($string, $data, array($this, 'parser_callback'), TRUE);

         if ($inject_noparse)
         {
             $parsed = Lex_Parser::inject_noparse($parsed);
         }

         // Return results or not ?
         if ( ! $return)
         {
             $this->_ci->output->append_output($parsed);
             return;
         }

         return $parsed;
     }

    // --------------------------------------------------------------------

    /**
     * Callback from template parser
     *
     * @param   array
     * @return   mixed
     */
    public function parser_callback($plugin, $attributes, $content, $data)
    {
        $this->_ci->load->library('plugins');
        $return_data = '';

        // Check if there were any custom callbacks defined
        if (isset($this->callbacks[$plugin]))
        {
            $callback = $this->callbacks[$plugin];

            if (is_callable($callback))
            {
                $return_data = call_user_func_array($callback, array($plugin, $attributes, $content, $data));
            }
        }
        else
        {
            // Locate and process plugin
            $return_data = $this->_ci->plugins->locate($plugin, $attributes, $content, $data);
        }

        if (is_array($return_data))
        {
            if ( ! $this->_is_multi($return_data))
            {
                $return_data = $this->_make_multi($return_data);
            }

            // Check if plugin has made any changes to the default content
            if (isset($return_data['_content']))
            {
                $content = $return_data['_content'];
                unset($return_data['_content']);
            }

            $parsed_return = '';

            $parser = new Lex_Parser();
            $parser->scope_glue(':');

            foreach ($return_data as $result)
            {
                // Check if there was content declared for the result
                // If no _content declared in result array use default content
                if (isset($result['_content']))
                {
                    $rendered_content = $result['_content'];
                    unset($result['_content']);
                }
                else
                {
                    $rendered_content = $content;
                }

                $parsed_return .= $parser->parse($rendered_content, $result, array($this, 'parser_callback'));
            }
            unset($parser);

            $return_data = $parsed_return;
        }

        return $return_data ? $return_data : NULL;
    }

     // ------------------------------------------------------------------------

     /**
      * Ensure we have a multi array
      *
      * @param   array
      * @return   int
      */
     private function _is_multi($array)
     {
         return (count($array) != count($array, 1));
     }

     // --------------------------------------------------------------------

     /**
      * Forces a standard array in multidimensional.
      *
      * @param   array
      * @param   int     Used for recursion
      * @return  array   The multi array
      */
     private function _make_multi($flat, $i=0)
     {
         $multi = array();
         $return = array();
         foreach ($flat as $item => $value)
         {
             $return[$i][$item] = $value;
         }
         return $return;
     }

     // --------------------------------------------------------------------

     /**
      * Forces a standard array in multidimensional.
      *
      * @param   array
      * @param   int     Used for recursion
      * @return  array   The multi array
      */
     public function set_callback($trigger, $callback)
     {
         $this->callbacks[$trigger] = $callback;
     }
 }

 // END MY_Parser Class

 /* End of file MY_Parser.php */
 /* Location: ./application/libraries/MY_Parser.php */

