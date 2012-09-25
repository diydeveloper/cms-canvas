<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Formation
 *
 * A CodeIgniter library that creates forms via a config file.  It
 * also contains functions to allow for creation of forms on the fly.
 *
 * @package     Formation
 * @author      Dan Horrigan <http://dhorrigan.com>
 * @license     Apache License v2.0
 * @copyright   2010 Dan Horrigan
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Core Formation Class
 *
 * @subpackage  Formation
 */
class Formation
{
    /**
     * Used to store the global CI instance
     */
    private $_ci;

    /**
     * Used to store the configuration
     */
    private $_config = array();

    /**
     * Used to store the forms
     */
    private $_form = array();

    /**
     * Used to store the form_validation info
     */
    private $_validation = array();

    /**
     * Valid types for input tags (including HTML5)
     */
    private $_valid_inputs = array(
        'button','checkbox','color','date','datetime',
        'datetime-local','email','file','hidden','image',
        'month','number','password','radio','range',
        'reset','search','submit','tel','text','time',
        'url','week'
    );

    // --------------------------------------------------------------------

    /**
     * Construct
     *
     * Imports the global config and custom config (if given).  We have this
     * to support CI's loader which calls the construct.
     *
     * @access  public
     * @param   array   $custom_config
     */
    public function __construct($custom_config = array())
    {
        $this->init($custom_config);
    }

    // --------------------------------------------------------------------

    /**
     * Init
     *
     * Imports the global config and custom config (if given) and initializes
     * the global CI instance.
     *
     * @access  public
     * @param   array   $custom_config
     */
    public function init($custom_config = array())
    {
        $this->_ci =& get_instance();

        // Include the formation config and ensure it is formatted
        if (file_exists(APPPATH . 'config/formation.php'))
        {
            include(APPPATH . 'config/formation.php');
            if ( ! isset($formation) OR !is_array($formation))
            {
                show_error('Formation config is not formatted correctly.');
            }
            $this->add_config($formation);
        }
        else
        {
            show_error('Formation config file is missing.');
        }

        // Merge the custom config into the global config
        if ( is_array($custom_config) && ! empty($custom_config))
        {
            $attributes = $custom_config;
            $fields = $attributes['fields'];
            unset($attributes['fields']);

            $this->_add_form($attributes, $fields);
        }
        elseif ( is_string($custom_config) && ! empty($custom_config))
        {
            $attributes = $formation['forms'][$custom_config];
            $fields = $attributes['fields'];
            unset($attributes['fields']);

            $this->_add_form($attributes, $fields);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Add Config
     *
     * Merges a config array into the current config
     *
     * @access  public
     * @param   array   $config
     * @return  void
     */
    public function add_config($config)
    {
        unset($config['forms']);
        $this->_config = array_merge_recursive($this->_config, $config);
    }

    // --------------------------------------------------------------------

    /**
     * Add Form
     *
     * Adds a form to the config
     *
     * @access  public
     * @param   array   $attributes
     * @param   array   $fields
     * @return  void
     */
    public function _add_form($attributes = array(), $fields = array())
    {
        $this->_form['attributes'] = $attributes;
        $this->add_fields($fields);

        $this->parse_validation();
    }

    // --------------------------------------------------------------------

    /**
     * Add Field
     *
     * Adds a field to a given form
     *
     * @access  public
     * @param   string  $field_name
     * @param   array   $attributes
     * @return  void
     */
    public function add_field($field_name, $attributes)
    {
        if ($this->field_exists($field_name))
        {
            show_error(sprintf('Field "%s" already exists in this form".  If you were trying to modify the field, please use Formation::modify_field($field_name, $attributes).', $field_name));
        }

        $this->_form['fields'][$field_name] = $attributes;

        if ($attributes['type'] == 'file')
        {
            $this->_form['attributes']['enctype'] = 'multipart/form-data';
        }

        $this->parse_validation();
    }

    // --------------------------------------------------------------------

    /**
     * Add Fields
     *
     * Allows you to add multiple fields at once.
     *
     * @access  public
     * @param   array   $fields
     * @return  void
     */
    public function add_fields($fields)
    {
        foreach ($fields as $field_name => $attributes)
        {
            $this->add_field($field_name, $attributes);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Modify Field
     *
     * Allows you to modify a field.
     *
     * @access  public
     * @param   string  $field_name
     * @param   array   $attributes
     * @return  void
     */
    public function modify_field($field_name, $attributes)
    {
        if ( ! $this->field_exists($field_name))
        {
            show_error(sprintf('Field "%s" does not exist in this form.', $field_name));
        }
        $this->_form['fields'][$field_name] = array_merge($this->_form['fields'][$field_name], $attributes);

        $this->parse_validation();
    }

    // --------------------------------------------------------------------

    /**
     * Modify Fields
     *
     * Allows you to modify multiple fields at once.
     *
     * @access  public
     * @param   array   $fields
     * @return  void
     */
    public function modify_fields($fields)
    {
        foreach ($fields as $field_name => $attributes)
        {
            $this->modfy_field($field_name, $attributes);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Field Exists
     *
     * Checks if a field exists.
     *
     * @param   string  $field_name
     * @return  bool
     */
    public function field_exists($field_name)
    {
        return isset($this->_form['fields'][$field_name]);
    }

    // --------------------------------------------------------------------

    /**
     * Form
     *
     * Builds a form and returns well-formatted, valid XHTML for output.
     *
     * @access  public
     * @return  string
     */
    public function form()
    {
        $form = $this->_form;

        $return = $this->open() . "\n";
        $return .= $this->fields(null, null, true);
        $return .= $this->close() . "\n";

        return $return;
    }

    // --------------------------------------------------------------------

    /**
     * Field
     *
     * Builds a field and returns well-formatted, valid XHTML for output.
     *
     * @access  public
     * @param   string  $name
     * @param   string  $properties
     * @return  string
     */
    public function label_field($name, $properties = array())
    {
        $return = '';

        if ( ! isset($properties['name']))
        {
            $properties['name'] = $name;
        }
        $required = FALSE;
        if (isset($this->_validation))
        {
            foreach ($this->_validation as $rule)
            {
                if ($rule['field'] == $properties['name'] AND $rule['rules'] AND strpos($rule['rules'], 'required') !== FALSE)
                {
                    $required = TRUE;
                }
            }
        }

        $return .= $this->_open_field($properties['type'], $required);
        
        switch($properties['type'])
        {
            case 'hidden':
                $return .= "\t\t" . $this->input($properties) . "\n";
                break;
            case 'radio': case 'checkbox':
                $return .= "\t\t\t" . sprintf($this->_config['label_wrapper_open'], $name) . ($required && $this->_config['required_location'] == 'label_before' ? $this->_config['required_tag']  : '') . $properties['label'] . ($required && $this->_config['required_location'] == 'label_after' ? $this->_config['required_tag']  : '') . $this->_config['label_wrapper_close'] . "\n";
                if (isset($properties['items']))
                {
                    $return .= "\t\t\t<span>\n";
                    
                    if ($properties['type'] == 'checkbox' && count($properties['items']) > 1)
                    {
                        // More than one item exists, this should probably be an array
                        if (substr($properties['name'], -2) != '[]')
                        {
                            $properties['name'] .= '[]';
                        }
                    }

                    foreach ($properties['items'] as $count => $element)
                    {
                        if ( ! isset($element['id']))
                        {
                            $element['id'] = str_replace('[]', '', $name) . '_' . $count;
                        }
                        
                        $element['type'] = $properties['type'];
                        $element['name'] = $properties['name'];
                        $return .= "\t\t\t\t" . $this->input($element) . "\n";
                        $return .= "\t\t\t\t" . sprintf($this->_config['label_wrapper_open'], $element['id']) . $element['label'] . $this->_config['label_wrapper_close'] . "\n";
                    }
                    $return .= "\t\t\t</span>\n";
                }
                else
                {
                    $return .= "\t\t\t" . sprintf($this->_config['label_wrapper_open'], $name) . $properties['label'] . $this->_config['label_wrapper_close'] . "\n";
                    $return .= "\t\t\t" . $this->input($properties) . "\n";
                }
                break;
            case 'select':
                $return .= "\t\t\t" . sprintf($this->_config['label_wrapper_open'], $name) . ($required && $this->_config['required_location'] == 'label_before' ? $this->_config['required_tag']  : '') . $properties['label'] . ($required && $this->_config['required_location'] == 'label_after' ? $this->_config['required_tag']  : '') . $this->_config['label_wrapper_close'] . "\n";
                $return .= "\t\t\t" . $this->select($properties, 3) . "\n";
                break;
            case 'textarea':
                $return .= "\t\t\t" . sprintf($this->_config['label_wrapper_open'], $name) .($required && $this->_config['required_location'] == 'label_before' ? $this->_config['required_tag']  : '') . $properties['label'] . ($required && $this->_config['required_location'] == 'label_after' ? $this->_config['required_tag']  : '') . $this->_config['label_wrapper_close'] . "\n";
                $return .= "\t\t\t" . $this->textarea($properties) . "\n";
                break;
            default:
                $return .= "\t\t\t" . sprintf($this->_config['label_wrapper_open'], $name) . ($required && $this->_config['required_location'] == 'label_before' ? $this->_config['required_tag']  : '') . $properties['label'] . ($required && $this->_config['required_location'] == 'label_after' ? $this->_config['required_tag']  : '') . $this->_config['label_wrapper_close'] . "\n";
                $return .= "\t\t\t" . $this->input($properties) . "\n";
                break;
        }

        $return .= $this->_close_field($properties['type'], $required);

        return $return;
    }

    // --------------------------------------------------------------------
    
    /**
     * Open Field
     *
     * Generates the fields opening tags.
     *
     * @access  private
     * @param   string  $type
     * @param   bool    $required
     * @return  string
     */
    private function _open_field($type, $required = FALSE)
    {
        if($type == 'hidden')
        {
            return '';
        }

        $return = "\t\t" . $this->_config['input_wrapper_open'] . "\n";

        if ($required AND $this->_config['required_location'] == 'before')
        {
            $return .= "\t\t\t" . $this->_config['required_tag'] . "\n";
        }
        
        return $return;
    }

    // --------------------------------------------------------------------
    
    /**
     * Close Field
     *
     * Generates the fields closing tags.
     *
     * @access  private
     * @param   string  $type
     * @param   bool    $required
     * @return  string
     */
    private function _close_field($type, $required = FALSE)
    {
        if($type == 'hidden')
        {
            return '';
        }
        
        $return = "";

        if ($required AND $this->_config['required_location'] == 'after')
        {
            $return .= "\t\t\t" . $this->_config['required_tag'] . "\n";
        }

        $return .= "\t\t" . $this->_config['input_wrapper_close'] . "\n";
        
        return $return;
    }
    
    // --------------------------------------------------------------------

    /**
     * Select
     *
     * Generates a <select> element based on the given parameters
     *
     * @access  public
     * @param   array   $parameters
     * @param   int     $indent_amount
     * @return  string
     */
    public function select($parameters, $indent_amount = 0)
    {
        if ( ! isset($parameters['options']) OR !is_array($parameters['options']))
        {
            show_error(sprintf('Select element "%s" is either missing the "options" or "options" is not array.', $parameters['name']));
        }
        // Get the options then unset them from the array
        $options = $parameters['options'];
        unset($parameters['options']);

        // Get the selected options then unset it from the array
        $selected = $parameters['selected'];
        unset($parameters['selected']);

        $input = "<select " . $this->_attr_to_string($parameters) . ">\n";
        foreach ($options as $key => $val)
        {
            if (is_array($val))
            {
                $input .= str_repeat("\t", $indent_amount + 1) . '<optgroup label="' . $key . '">' . "\n";
                foreach ($val as $opt_key => $opt_val)
                {
                    $extra = ($opt_key == $selected) ? ' selected="selected"' : '';
                    $input .= str_repeat("\t", $indent_amount + 2);
                    $input .= '<option value="' . $opt_key . '"' . $extra . '>' . $this->prep_value($opt_val) . "</option>\n";
                }
                $input .= str_repeat("\t", $indent_amount + 1) . "</optgroup>\n";
            }
            else
            {
                $extra = ($key == $selected) ? ' selected="selected"' : '';
                $input .= str_repeat("\t", $indent_amount + 1);
                $input .= '<option value="' . $key . '"' . $extra . '>' . $this->prep_value($val) . "</option>\n";
            }
        }
        $input .= str_repeat("\t", $indent_amount) . "</select>";

        return $input;
    }

    // --------------------------------------------------------------------

    /**
     * Open
     *
     * Generates the opening <form> tag
     *
     * @access  public
     * @param   string  $action
     * @param   array   $options
     * @return  string
     */
    public function open($options = array())
    {
        $form = $this->_form;
        $options = array_merge($form['attributes'], $options);

        // If there is still no action set, self-post
        if (! isset($options['action']) && empty($options['action']))
        {
            $options['action'] = $this->_ci->uri->uri_string();
        }

        // If not a full URL, create one with CI
        if ( ! strpos($options['action'], '://'))
        {
            $options['action'] = $this->_ci->config->site_url($options['action']);
        }

        // If method is empty, use POST
        isset($options['method']) OR $options['method'] = 'post';

        $form = '<form ' . $this->_attr_to_string($options) . '>';

        return $form;
    }

    // --------------------------------------------------------------------

    /**
     * Fields
     *
     * Generates the list of fields without the form open and form close tags
     *
     * @access  public
     * @param   string  $action
     * @param   array   $options
     * @return  string
     */
    public function fields($start_field_name = NULL, $end_field_name = NULL, $form_wrapper = FALSE)
    {
        $hidden = array();
        $return = '';
        $form = $this->_form;

        $form['fields'] = $this->_array_slice($form['fields'], $start_field_name, $end_field_name);

        if($form_wrapper)
        {
            $return = "\t" . $this->_config['form_wrapper_open'] . "\n";
        }

        foreach ($form['fields'] as $name => $properties)
        {
            if($properties['type'] == 'hidden')
            {
                $hidden[$name] = $properties;
                continue;
            }
            $return .= $this->label_field($name, $properties);
        }

        if($form_wrapper)
        {
            $return .= "\t" . $this->_config['form_wrapper_close'] . "\n";
        }
        
        foreach ($hidden as $name => $properties)
        {
            if ( ! isset($properties['name']))
            {
                $properties['name'] = $name;
            }
            $return .= "\t" . $this->input($properties) . "\n";
        }
        
        return $return;
    }

    // --------------------------------------------------------------------

    /**
     * Close
     *
     * Generates the closing </form> tag
     *
     * @access  public
     * @return  string
     */
    public function close()
    {
        return '</form>';
    }

    // --------------------------------------------------------------------

    /**
     * Label
     *
     * Generates a label based on given parameters
     *
     * @access  public
     * @param   string  $value
     * @param   string  $for
     * @return  string
     */
    public function label($field_name)
    {
        $form = $this->_form;
        
        if (isset($form['fields'][$field_name]))
        {
            $properties = $form['fields'][$field_name];
            return sprintf($this->_config['label_wrapper_open'], $field_name) . ($required && $this->_config['required_location'] == 'label_before' ? $this->_config['required_tag']  : '') . $properties['label'] . ($required && $this->_config['required_location'] == 'label_after' ? $this->_config['required_tag']  : '') . $this->_config['label_wrapper_close'] . "\n";
        }
        else
        {
            show_error(sprintf('"%s" not found in field list.', $field_name));
        }
    }

    // --------------------------------------------------------------------

    /**
     * Input
     *
     * Generates an <input> tag
     *
     * @access  public
     * @param   array   $options
     * @return  string
     */
    public function input($options)
    {
        if ( ! isset($options['type']))
        {
            show_error('You must specify a type for the input.');
        }
        elseif ( ! in_array($options['type'], $this->_valid_inputs))
        {
            show_error(sprintf('"%s" is not a valid input type.', $options['type']));
        }
        $input = '<input ' . $this->_attr_to_string($options) . ' />';

        return $input;
    }

    // --------------------------------------------------------------------

    /**
     * Textarea
     *
     * Generates a <textarea> tag
     *
     * @access  public
     * @param   array   $options
     * @return  string
     */
    public function textarea($options)
    {
        $value = '';
        if (isset($options['value']))
        {
            $value = $options['value'];
            unset($options['value']);
        }
        $input = "<textarea " . $this->_attr_to_string($options) . '>';
        $input .= $this->prep_value($value);
        $input .= '</textarea>';

        return $input;
    }


    // --------------------------------------------------------------------

    /**
     * Attr to String
     *
     * Takes an array of attributes and turns it into a string for an input
     *
     * @access  private
     * @param   array   $attr
     * @return  string
     */
    private function _attr_to_string($attr)
    {
        $attr_str = '';

        if ( ! is_array($attr))
        {
            $attr = (array) $attr;
        }

        foreach ($attr as $property => $value)
        {
            if ($property == 'label')
            {
                continue;
            }
            if ($property == 'value')
            {
                $value = $this->prep_value($value);
            }
            $attr_str .= $property . '="' . $value . '" ';
        }

        // We strip off the last space for return
        return substr($attr_str, 0, -1);
    }

    // --------------------------------------------------------------------

    /**
     * Prep Value
     *
     * Prepares the value for display in the form
     *
     * @access  public
     * @param   string  $value
     * @return  string
     */
    public function prep_value($value)
    {
        $value = $value;
        $value = str_replace(array("'", '"'), array("&#39;", "&quot;"), $value);

        return $value;
    }

    // --------------------------------------------------------------------

    /**
     * Parse Validation
     *
     * Adds the validation rules in each field to the $_validation array
     * and removes it from the field attributes
     *
     * @access  private
     * @return  void
     */
    private function parse_validation()
    {
        $form = $this->_form;

        if ( ! isset($form['fields']))
        {
            return;
        }

        $i = 0;
        foreach ($form['fields'] as $name => $attr)
        {
            if (isset($attr['validation']))
            {
                $this->_validation[$i]['field'] = $name;
                $this->_validation[$i]['label'] = $attr['label'];
                $this->_validation[$i]['rules'] = $attr['validation'];

                unset($this->_form['fields'][$name]['validation']);
            }

            ++$i;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Validate
     *
     * Runs form validation on the given form
     *
     * @access  public
     * @return  bool
     */
    public function validate()
    {
        if ( ! isset($this->_validation))
        {
            return TRUE;
        }
        $this->_load_validation();

        $this->_ci->form_validation->set_rules($this->_validation);

        return $this->_ci->form_validation->run();
    }

    // --------------------------------------------------------------------

    /**
     * Error
     *
     * Returns a single form validation error
     *
     * @access  public
     * @param   string  $field_name
     * @param   string  $prefix
     * @param   string  $suffix
     * @return  string
     */
    public function error($field_name, $prefix = '', $suffix = '')
    {
        $this->_load_validation();

        return $this->_ci->form_validation->error($field_name, $prefix, $suffix);
    }

    // --------------------------------------------------------------------

    /**
     * All Errors
     *
     * Returns all of the form validation errors
     *
     * @access  public
     * @param   string  $prefix
     * @param   string  $suffix
     * @return  string
     */
    public function all_errors($prefix = '', $suffix = '')
    {
        $this->_load_validation();

        return $this->_ci->form_validation->error_string($prefix, $suffix);
    }

    // --------------------------------------------------------------------

    /**
     * Set Value
     *
     * Set's a fields value
     *
     * @access  public
     * @param   string  $field_name
     * @param   mixed   $value
     * @return  void
     */
    public function set_value($field_name, $default = NULL)
    {
        $this->_load_validation();

        $value = (is_array($default)) ? $default : $this->prep_value($default);

        $field =& $this->_form['fields'][$field_name];

        switch($field['type'])
        {
            case 'radio': case 'checkbox':
                if (isset($field['items']))
                {
                    foreach ($field['items'] as &$element)
                    {
                        if (is_array($value))
                        {
                            if (in_array($element['value'], $value))
                            {
                                $element['checked'] = 'checked';
                            }
                            else
                            {
                                if (isset($element['checked']))
                                {
                                    unset($element['checked']);
                                }
                            }
                        }
                        else
                        {
                            if ($element['value'] === $value)
                            {
                                $element['checked'] = 'checked';
                            }
                            else
                            {
                                if (isset($element['checked']))
                                {
                                    unset($element['checked']);
                                }
                            }
                        }
                    }
                }
                else
                {
                    $field['value'] = $value;
                }
                break;
            case 'select':
                $field['selected'] = $value;
                break;
            default:
                $field['value'] = $this->prep_value($value);
                break;
        }
    }

    // --------------------------------------------------------------------

    /**
     * populate
     *
     * Repopulates the entire form with the submitted data.
     *
     * @access  public
     * @return  string
     */
    public function populate($data = null, $repopulate = TRUE)
    {
        $this->_load_validation();

        foreach ($this->_form['fields'] as $field_name => $attr)
        {
            $field_name = str_replace('[]', '', $field_name);

            if($repopulate && isset($_POST[$field_name]))
            {
                $this->set_value($field_name, $_POST[$field_name]);
            }
            elseif(is_array($data) && isset($data[$field_name]))
            {
                $this->set_value($field_name, $data[$field_name]);
            }
            elseif(is_object($data) && isset($data->$field_name))
            {
                $this->set_value($field_name, $data->$field_name);
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Load Validation
     *
     * Checks if the form_validation library is loaded.  If it is not it loads it.
     *
     * @access  private
     * @return  void
     */
    private function _load_validation()
    {
        if ( ! class_exists('CI_Form_validation'))
        {
            $this->_ci->load->library('form_validation');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Field
     *
     * Returns a field buit from the config
     *
     * @access  public
     * @param   string   $field_name
     * @return  string
     */
    public function field($field_name)
    {
        $hidden = array();
        $form = $this->_form;

        if (isset($form['fields'][$field_name]) && $properties = $form['fields'][$field_name])
        {
            if($properties['type'] == 'hidden')
            {
                if ( ! isset($properties['name']))
                {
                    $properties['name'] = $name;
                }
                return "\t" . $this->input($properties) . "\n";
            }

            $return = '';
            switch($properties['type'])
            {
                case 'hidden':
                    $return .= "\t\t" . $this->input($properties) . "\n";
                    break;
                case 'radio': case 'checkbox':
                    if (isset($properties['items']))
                    {
                        $return .= "\t\t\t<span>\n";
                        
                        if ($properties['type'] == 'checkbox' && count($properties['items']) > 1)
                        {
                            // More than one item exists, this should probably be an array
                            if (substr($properties['name'], -2) != '[]')
                            {
                                $properties['name'] .= '[]';
                            }
                        }

                        foreach ($properties['items'] as $count => $element)
                        {
                            if ( ! isset($element['id']))
                            {
                                $element['id'] = str_replace('[]', '', $name) . '_' . $count;
                            }
                            
                            $element['type'] = $properties['type'];
                            $element['name'] = $properties['name'];
                            $return .= "\t\t\t\t" . $this->input($element) . "\n";
                            $return .= "\t\t\t\t" . sprintf($this->_config['label_wrapper_open'], $element['id']) . ($required && $this->_config['required_location'] == 'label_before' ? $this->_config['required_tag']  : '') . $element['label'] . ($required && $this->_config['required_location'] == 'label_after' ? $this->_config['required_tag']  : '') . $this->_config['label_wrapper_close'] . "\n";
                        }
                        $return .= "\t\t\t</span>\n";
                    }
                    else
                    {
                        $return .= "\t\t\t" . sprintf($this->_config['label_wrapper_open'], $name) . ($required && $this->_config['required_location'] == 'label_before' ? $this->_config['required_tag']  : '') . $properties['label'] . ($required && $this->_config['required_location'] == 'label_after' ? $this->_config['required_tag']  : '') . $this->_config['label_wrapper_close'] . "\n";
                        $return .= "\t\t\t" . $this->input($properties) . "\n";
                    }
                    break;
                case 'select':
                    $return .= "\t\t\t" . $this->select($properties, 3) . "\n";
                    break;
                case 'textarea':
                    $return .= "\t\t\t" . $this->textarea($properties) . "\n";
                    break;
                default:
                    $return .= "\t\t\t" . $this->input($properties) . "\n";
                    break;
            }

            return $return;
        }
        
        show_error(sprintf('Field "%s" does not exist.', $field_name));
    }

    // --------------------------------------------------------------------

    /**
     * Array Slice
     *
     * Returns an array slice of a fields array
     *
     * @access  private
     * @param   array    $field_array
     * @param   string   $start_field_name
     * @param   string   $end_field_name
     * @return  array
     */
    private function _array_slice($field_array, $start_field_name = null, $end_field_name = null)
    {
        $array_element = false;
        $return_array = array();

        foreach($field_array as $field_name=>$properties)
        {
            if(is_null($start_field_name) ||  $field_name == $start_field_name)
            {
                $array_element = true;
            }

            if($array_element)
            {
                $return_array[$field_name] = $properties;
            }

            if($field_name == $end_field_name)
            {
                break;
            }
        }

        return $return_array;
    }

    public function set_options($field_name, $options)
    {
        $this->modify_field($field_name, array('options' => $options));
    }

    public function set_validation($field_name, $validation)
    {
        $this->modify_field($field_name, array('validation' => $validation));
    }
}

/* End of file Formation.php */
