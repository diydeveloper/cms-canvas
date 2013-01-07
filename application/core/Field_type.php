<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Field_type extends CI_Model
{
    protected $Entry = null;
    protected $Field = null;
    protected $content = null;
    protected $CI = null;

    public function __construct()
    {
        $this->CI =& get_instance();     
    }

    // ------------------------------------------------------------------------
    
    /*
     * Factory
     *
     * This static function takes data from various sources and attempts to build a
     * a specific field type object populated with a clean and consistent data format.
     * 
     * @param object
     * @param object
     * @param object
     * @param string
     * @return object
     */
    public static function factory($model_name, $Field = null, $Entry = null, $Entry_data = null) 
    {
        $CI =& get_instance();

        $classname = $model_name . '_field';

        include_once APPPATH . '/modules/content/content_fields/models/' . $classname . '.php';
        $Field_type = new $classname;

        // Populate field type object with data
        if ( ! empty($Field))
        {
            $Field_type->set_field($Field);
        }

        if ( ! empty($Entry))
        {
            $Field_type->set_entry($Entry);
        }

        if (is_object($Entry_data) && isset($Entry_data->{'field_id_' . $Field->id}))
        {
            $Field_type->set_content($Entry_data->{'field_id_' . $Field->id});
        }
        else if (is_object($Entry) && isset($Entry->{'field_id_' . $Field->id}))
        {
            $Field_type->set_content($Entry->{'field_id_' . $Field->id});
        }
        else
        {
            $Field_type->set_content('');
        }

        return $Field_type;
    }

    // ------------------------------------------------------------------------

    /*
     * Display Field
     *
     * Returns the administrative field used to edit the content
     *
     * @return string
     */
    public function display_field()
    {
        return '';
    }

    // ------------------------------------------------------------------------

    /*
     * Output
     *
     * Returns the final rendering of the content
     *
     * @return string
     */
    public function output() 
    { 
        return $this->content;
    }

    // ------------------------------------------------------------------------

    /*
     * Save
     *
     * Returns the content in its format that is to be stored in the database
     *
     * @return string
     */
    public function save() 
    { 
        return $this->content;
    }

    // ------------------------------------------------------------------------

    /*
     * Settings
     *
     * Returns additional administrative setting fields for the field type
     *
     * @return string
     */
    public function settings() 
    { 
        return '';
    }

    // ------------------------------------------------------------------------

    /*
     * Validate
     *
     * Form validation for the display field. Returns true if everything is good.
     *
     * @return bool
     */
    public function validate()
    {
        $this->CI->form_validation->set_rules('field_id_' . $this->Field->id, $this->Field->label, 'trim' . (($this->Field->required) ? '|required' : ''));

        return TRUE;
    }

    // ------------------------------------------------------------------------

    /*
     * Inline Validate
     *
     * Validation for inline content. Returns true if everything is good.
     *
     * @return bool
     */
    public function inline_validate()
    {
        $this->CI->form_validation->set_rules('cc_field_' . $this->Entry->id . '_' . $this->Field->id, $this->Field->label, 'trim');

        return TRUE;
    }

    // ------------------------------------------------------------------------

    /*
     * Set Entry
     *
     * Used by factory to format and set a consistent stdClass with the entry data
     *
     * @return void
     */
    public function set_entry($Entry_mixed)
    {
        $entry_properties = array(
                'id',
                'slug',
                'title',
                'url_title',
                'required',
                'content_type_id',
                'status',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'created_date',
                'modified_date',
                'author_id',
            );

        $Entry = new stdClass();
        foreach ($entry_properties as $property)
        {
            $Entry->$property = $Entry_mixed->$property;
        }

        $this->Entry = $Entry;
    }

    // ------------------------------------------------------------------------

    /*
     * Set Field
     *
     * Used by factory to format and set a consistent stdClass of the field data
     *
     * @return void
     */
    public function set_field($Field_mixed)
    {
        $field_properties = array(
                'id',
                'content_type_id',
                'content_field_type_id',
                'label',
                'short_tag',
                'required',
                'options',
                'settings',
                'sort',
            );

        $Field = new stdClass();
        foreach ($field_properties as $property)
        {
            $Field->$property = $Field_mixed->$property;
        }

        $Field->settings = @unserialize($Field->settings);

        $this->Field = $Field;
    }

    // ------------------------------------------------------------------------

    /*
     * Set Content
     *
     * Used to set the current field type's content as a class variable
     *
     * @return void
     */
    public function set_content($content)
    {
        $this->content = ($content != '') ? $content : null;
    }

    // ------------------------------------------------------------------------

    /*
     * Is Inline Editable
     *
     * Checks if the current field type, user permissions, and current settings allow for inline editing of the content
     *
     * @return bool
     */
    public function is_inline_editable()
    {
        if (empty($this->Entry->content_type_id))
        {
            return FALSE;
        }

        return is_inline_editable($this->Entry->content_type_id);
    }
}
