<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Field_type extends CI_Model
{
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
    public static function factory($Field = null, $Entry = null, $Entry_data = null, $model_name = null) 
    {
        $CI =& get_instance();

        if ( ! empty($model_name))
        {
            $classname = $model_name;
        }
        else if ( ! empty($Field))
        {
            if ($Field instanceof Content_fields_model)
            {
                $classname = $Field->content_field_types_model_name . '_field';
            }
            else
            {
                $classname = $Field->model_name . '_field';
            }
        }

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

        return $Field_type;
    }

    protected $Entry = null;
    protected $Field = null;
    protected $content = null;

    public function view()
    {
        return '';
    }

    public function output() 
    { 
        return $this->content;
    }

    public function save() 
    { 
        return $this->content;
    }

    public function settings() 
    { 
        return '';
    }

    public function validate()
    {
        return TRUE;
    }

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

        // Build options array
        $option_array = array();
        foreach (explode("\n", $Field->options) as $option)
        {
            $option = explode("=", $option, 2);
            $option_array[$option[0]] = (count($option) == 2) ? $option[1] : $option[0];
        }

        $Field->options = $option_array;

        $this->Field = $Field;
    }

    public function set_content($content)
    {
        $this->content = ($content != '') ? $content : null;
    }
}
