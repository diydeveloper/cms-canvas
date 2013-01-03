<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Fields extends Admin_Controller {

	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
        $data = array();
        $data['breadcrumb'] = set_crumbs(array('content/types' => 'Content Types', current_url() => 'Content Fields'));
        $type_id = $this->uri->segment(5);
        $this->template->add_package('tablednd'); 

        $this->load->model('content_types_model');

        $data['Type'] = $Type = $this->content_types_model->get_by_id($type_id);

        if ( ! $Type->exists())
        {
            return show_404();
        }

        $data['Fields'] = $Type->content_fields->order_by('sort')->include_related('content_field_types', 'title')->get();

        $this->template->view('admin/fields/fields', $data);
	}

    function edit()
    {
        $Field = null;
        $data = array();
        $data['edit_mode'] = FALSE;
        $this->load->model('content_fields_model');
        $this->load->model('content_field_types_model');
        $this->load->model('content_types_model');

        $type_id = $this->uri->segment(5);
        $data['breadcrumb'] = set_crumbs(array('content/types' => 'Content Types', 'content/fields/index/' . $type_id => 'Content Fields', current_url() => 'Field Edit'));

        $data['Type'] = $Type = $this->content_types_model->get_by_id($type_id);

        // Check if content type exists
        if ( ! $Type->exists())
        {
            return show_404();
        }

        $field_id = $this->uri->segment(6);

        // Edit mode
        if ($field_id)
        {
            $data['edit_mode'] = TRUE;
            $data['Field'] = $Field = $this->content_fields_model
                ->include_related('content_field_types', 'model_name')
                ->get_by_id($field_id);

            // Check if field exists
            if ( ! $Field->exists())
            {
                return show_404();
            }
        }

        // Get content field types for dropdown and a datatype refrence
        $Content_field_types = $this->content_field_types_model->order_by('title')->get();
        $datatype_ref_array = option_array_value($Content_field_types, 'id', 'datatype');
        $data['Content_field_types'] = option_array_value($Content_field_types, 'id', 'title');

        // Get setting fields
        $this->load->add_package_path(APPPATH . 'modules/content/content_fields');
        $Content_fields = $this->load->library('content_fields');
        $data['setting_fields'] = $Content_fields->settings($Field);

        // Form Validation
        $this->form_validation->set_rules('content_field_type_id', 'Type', 'trim|required');
        $this->form_validation->set_rules('label', 'Label', 'trim|required');
        $this->form_validation->set_rules('required', 'Required', 'trim|required');

        if ($data['edit_mode'])
        {
            $this->form_validation->set_rules('short_tag', 'Short Tag', 'trim|alpha_dash|required|callback_unique_short_tag[' . $Field->short_tag . ']');
        }
        else
        {
            $this->form_validation->set_rules('short_tag', 'Short Tag', 'trim|alpha_dash|required|callback_unique_short_tag');
        }


        if ($this->form_validation->run() == TRUE)
        {
            $this->load->model('content_fields_model');

            $Content_fields = new Content_fields_model();
            $Content_fields->from_array($this->input->post());
            $Content_fields->content_type_id = $type_id;
            $Content_fields->short_tag = $this->input->post('short_tag');

            // Setting fields
            $Content_fields->settings = ($this->input->post('settings')) ? serialize($this->input->post('settings')) : NULL;

            // Edit mode
            if ($field_id)
            {
                $Content_fields->id = $field_id;
            }

            $Content_fields->save();

            // If new record add column to entries data and set sort
            if ( ! $data['edit_mode'])
            {
                // Set a sort number so that the field will be 
                // added to the end of the fields list. 
                // Setting it to its ID# ensures that it is greater than the other field's sort
                $Content_fields->sort = $Content_fields->id;
                $Content_fields->save();
            }

            $this->load->library('cache');
            $this->cache->delete_all('content_types');
            $this->cache->delete_all('entries');

            // There is probably a better way to go about getting the
            // field datatype but this should work for now
            $datatype = isset($datatype_ref_array[$Content_fields->content_field_type_id]) ? $datatype_ref_array[$Content_fields->content_field_type_id] : 'text';
            $Content_fields->save_entries_column($datatype);

            $this->session->set_flashdata('message', '<p class="success">Content field saved successfully.</p>');

            redirect(ADMIN_PATH . '/content/fields/index/' . $Type->id);
        }


        $this->template->view('admin/fields/edit', $data);
    }

    function delete()
    {
        $this->load->helper('file');
        $this->load->model('content_fields_model');
        $type_id = $this->uri->segment(5);

        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(6);
        }

        $Content_fields = new Content_fields_model();
        $Content_fields->where_in('id', $selected)->get();

        if ($Content_fields->exists())
        {
            $message = '';

            foreach ($Content_fields as $Content_field)
            {
                $Content_field->drop_entries_column();
                $Content_field->delete();
            }

            // Clear cache so updates will show on next entry load
            $this->load->library('cache');
            $this->cache->delete_all('entries');
            $this->cache->delete_all('content_types');

            $message .= '<p class="success">The selected items were successfully deleted.</p>';

            $this->session->set_flashdata('message', $message);
        }

        redirect(ADMIN_PATH . '/content/fields/index/' . $type_id);
    }

    function order()
    {
        // Order fields
        if (is_ajax())
        {
            if(count($_POST) > 0 && $this->input->post('fields_table'))
            {
                $this->load->model('content_fields_model');

                $table_order = $this->input->post('fields_table');

                unset($table_order[0]);
                $table_order = array_values($table_order);

                $i = 1;
                foreach($table_order as $id)
                {
                    $Sort_fields = new Content_fields_model();
                    $Sort_fields->get_by_id($id);

                    if ($Sort_fields->exists())
                    {
                        $Sort_fields->sort = $i;
                        $Sort_fields->save();
                        unset($Sort_fields);
                        $i++;
                    }
                }
            }

            return;
        }
        else
        {
            return show_404();
        }
    }

    function settings()
    {
        // Check that this is an ajax request
        if ( ! is_ajax())
        {
            return show_404();
        }

        $this->load->add_package_path(APPPATH . 'modules/content/content_fields');
        $Content_fields = $this->load->library('content_fields');
        echo $Content_fields->settings();
    }

    function unique_short_tag($short_tag, $current_short_tag = '')
    {
        $Content_fields = new Content_fields_model();

        // If in edit mode ignore its current name
        if ($current_short_tag != '')
        {
            $Content_fields->where('short_tag !=', $current_short_tag);
        }

        $Content_fields->where('content_type_id', $this->uri->segment(5))
            ->where('short_tag', $short_tag)
            ->get();

        if ($Content_fields->exists())
        {
            $this->form_validation->set_message('unique_short_tag', 'The %s provided is already in use for this content type.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}

