<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Content_fields
{
    public $fields = array();
    public $Entry;
    public $CI;

    private $old_fields = array();

    function __construct()
    {
        $this->CI = get_instance();
        $this->CI->load->model('content/content_fields_model');
    }

    // ------------------------------------------------------------------------

    /*
     * Initialize
     *
     * Sets entry and entry fields as class variables
     *
     * @param array
     * @return void
     */
    function initialize($config)
    {
        $this->fields = $this->CI->content_fields_model
            ->order_by('sort', 'ASC')
            ->include_related('content_field_types', array('model_name', 'array_post'))
            ->get_by_content_type_id($config['content_type_id']);

        $this->Entry = $config['Entry'];

        // If content_id and entry content id do not match 
        // and convert is passed as 7th segment assume user is trying to convrt content
        if ($config['content_type_id'] != $this->Entry->content_type_id && $this->CI->uri->segment(7) == 'convert')
        {
            $this->change_content_type($this->Entry->content_type_id, $config['content_type_id']);
        }
        else
        {
            // Get entry data and set to class var
            $this->_get_entry_data();
        }

    }

    // ------------------------------------------------------------------------

    /*
     * Run
     *
     * Validates field and preps data executed at entry edit
     *
     * @return bool
     */
    function run()
    {
        $return = TRUE;

        foreach($this->fields as $Field)
        {
            // Load Field model and set field data
            $Field_model = $this->CI->load->model($Field->content_field_types_model_name . '_field');
            $data['Field'] = $Field;

            $this->CI->form_validation->set_rules('field_id_' . $Field->id . (($Field->content_field_types_array_post) ? '[]' : ''), $Field->label, 'trim' . (($Field->required) ? '|required' : ''));

            // Validate field command
            if ($Field_model->validate($data) === FALSE)
            {
                $return = FALSE;
            }
        }

        return $return;
    }

    // ------------------------------------------------------------------------

    /*
     * Settings
     *
     * Adds additional setting fields specific to the field type beging edited
     *
     * @return string
     */
    function settings($Field = null)
    {
        $data = array();

        // Set type
        if ($this->CI->input->post('content_field_type_id'))
        {
            // Look up content field type view name using posted type id
            $Field_type = $this->CI->load->model('content/content_field_types_model');
            $Field_type->select('model_name')->get_by_id($this->CI->input->post('content_field_type_id'));
            $type = ($Field_type->exists()) ? $Field_type->model_name : 'ckeditor';
        }
        else if ($Field != '' && $Field->exists())
        {
            $type = $Field->content_field_types_model_name;
        }
        else
        {
            $type = 'ckeditor';
        }

        $Field_model = $this->CI->load->model($type . '_field');

        // Get current field settings if an ajax request in edit mode
        if (is_ajax())
        {
            $field_id = $this->CI->input->post('field_id');

            // Edit mode
            if ($field_id)
            {
                $data['edit_mode'] = TRUE;
                $this->CI->load->model('content_fields_model');
                $Field = $this->CI->content_fields_model->get_by_id($field_id);
            }
        }

        // Pass field settings to model if exists
        if ($Field != '' && $Field->exists())
        {
            $Field->settings = @unserialize($Field->settings);
            $data['Field'] = $Field;
        }

        return $Field_model->settings($data); 
    }

    // ------------------------------------------------------------------------

    /*
     * Form
     *
     * Outputs field form fields at entry edit
     *
     * @return string
     */
    function form()
    {
        $form_views = '';

        foreach($this->fields as $Field)
        {
            // Load Field model and set field data
            $Field_model = $this->CI->load->model($Field->content_field_types_model_name . '_field');
            $data['Entry'] = $this->Entry;
            $data['Entry_data'] = $this->Entry_data;

            $Field->settings = @unserialize($Field->settings);

            // Build options array
            $option_array = array();
            foreach (explode("\n", $Field->options) as $option)
            {
                $option = explode("=", $option, 2);
                $option_array[$option[0]] = (count($option) == 2) ? $option[1] : $option[0];
            }

            $Field->options = $option_array;

            $data['Field'] = $Field;

            $form_views .= '<div>';
            $form_views .= '<label for="field_id_' . $Field->id . '"><div class="arrow arrow_expand"></div>' . (($Field->required) ? '<span class="required">*</span> ' : '') .  $Field->label .'</label>';

            $form_views .= '<div>';

            $form_views .= $Field_model->view($data); 

            $form_views .= '</div>';

            $form_views .= '</div>';
        }

        return $form_views;
    }

    // ------------------------------------------------------------------------

    /*
     * Save
     *
     * Saves field post data to database and remvoes unused fields from databse
     *
     * @return string
     */
    function save()
    {
        $result_array = array();

        $this->Entry_data->entry_id = $this->Entry->id;

        // Set empty post values to null
        foreach($this->fields as $Field)
        {
            // Load Field model and set field data
            $Field_model = $this->CI->load->model($Field->content_field_types_model_name . '_field');
            $data['Field'] = $Field;

            $value = $Field_model->save($data);

            if ($value == '')
            {
                $value = NULL;
            }

            $this->Entry_data->{'field_id_' . $Field->id} =  $value;
        }

        $this->Entry_data->save();
    }

    // ------------------------------------------------------------------------

    /*
     * Change Content Type
     *
     * Attempt to convert an entry's content type by matching short tags
     *
     * @param int
     * @return void
     */
    function change_content_type($old_content_type_id, $current_content_type_id)
    {
        $this->CI->load->model('content/content_types_model');
        $this->Entry->content_type_id = $current_content_type_id;

        // Check if a content type exists with this id
        $Content_type = new Content_types_model();
        $Content_type->get_by_id($old_content_type_id);

        if ($Content_type->exists())
        {
            // Get and set the new fields array which will now 
            // be used for validating and processing instead of the fields array
            $this->old_fields = new Content_fields_model();
            $this->old_fields->order_by('sort', 'ASC')
                ->include_related('content_field_types', 'model_name')
                ->get_by_content_type_id($old_content_type_id);

            // Get entry data
            $this->_get_entry_data();

            foreach($this->fields as $Field)
            {
                // If the field has a value set try to find
                // a matching tag name in the new content type's fields
                foreach($this->old_fields as $Old_field)
                {
                    if ($Field->short_tag == $Old_field->short_tag)
                    {
                        $this->Entry_data->{'field_id_' . $Field->id} = $this->Entry_data->{'field_id_' . $Old_field->id};
                    }
                }
            }

            // Set old fields to NULL
            foreach($this->old_fields as $Old_field)
            {
                $this->Entry_data->{'field_id_' . $Old_field->id} = NULL;
            }
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Get Entry Data
     *
     * Get entry data and sets to class variable
     *
     * @access private
     * @return void
     */
    private function _get_entry_data()
    {
        $select = 'id, entry_id';

        foreach ($this->fields as $Field)
        {
            $select .= ', field_id_' . $Field->id;
        }

        foreach ($this->old_fields as $Field)
        {
            $select .= ', field_id_' . $Field->id;
        }

        $this->Entry_data = $this->Entry
            ->entries_data
            ->select(trim($select, ', '))
            ->get();
    }
}
