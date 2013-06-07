<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Entries extends Admin_Controller {

	function __construct()
	{
		parent::__construct();	
	}
	
    // ------------------------------------------------------------------------

    /*
     * Index
     *
     * Display entries and apply any search filters

     * @return void
     */
	function index()
	{
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'Entries'));

        // Load libraries and models
        $this->load->model('entries_model');
        $this->load->model('content_types_model');
        $this->load->model('revisions_model');
        $this->load->library('pagination');
        $data['query_string'] = ( ! empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : '';
        $data['content_types_filter'] = array('' => '');
        $data['content_types_add_entry'] = array();

        // Process Filter using Admin Helper
        $filter = process_filter('entries');

        // Define fields the search filter searches
        $search = array();

        if (isset($filter['search']))
        {
            $search['title'] = $filter['search'];
            $search['slug'] = $filter['search'];
            $search['id'] = $filter['search'];
            unset($filter['search']);
        }

        // Pagination Settings
        $per_page = 50;

        // If user not a super admin only get the content types and entires allowed for access
        if ($this->Group_session->type != SUPER_ADMIN)
        {
            $this->content_types_model
                ->where('restrict_admin_access', 0)
                ->or_where_related('admin_groups', 'group_id', $this->Group_session->id);

            $this->entries_model
                ->group_start()
                ->where('restrict_admin_access', 0)
                ->or_where_related('content_types/admin_groups', 'group_id', $this->Group_session->id)
                ->group_end();
        }

        // Build content type filter dropdown 
        // and add entry's list of content types
        $Content_types = $this->content_types_model->order_by('title', 'asc')->get();

        foreach($Content_types as $Content_type)
        {
            $entries_count = $Content_type->entries->count();

            // Only add the content type to the Add Entry dropdown if it has not reached the
            // limit of entries allowed. An empty entries_allowed is unlimited
            if ($Content_type->entries_allowed == '' ||  $entries_count < $Content_type->entries_allowed || ($entries_count == 0 && $Content_type->entries_allowed > 0))
            {
                $data['content_types_add_entry'][$Content_type->id] = $Content_type->title;
            }

            // Only add the content type to the filter dropdown if it has one or more entries
            if ($entries_count > 0)
            {
                $data['content_types_filter'][$Content_type->id] = $Content_type->title;
            }
        }

        $this->entries_model->include_related('content_types', 'title');

        // Filter by search string
        if ( ! empty($search))
        {
            $this->entries_model
                ->group_start()
                ->or_like($search)
                ->group_end();
        }

        // Filter by dropdowns
        if ( ! empty($filter))
        {
            $this->entries_model
                ->group_start()
                ->where($filter)
                ->group_end();
        }

        // Finalize and sort entries query
        $data['Entries'] = $this->entries_model
            ->order_by(($this->input->get('sort')) ? $this->input->get('sort') : 'modified_date', ($this->input->get('order')) ? $this->input->get('order') : 'desc')
            ->get_paged($this->uri->segment(5), $per_page, TRUE);

        // Create Pagination
        $config['base_url'] = site_url(ADMIN_PATH . '/content/entries/index/');
        $config['total_rows'] = $data['Entries']->paged->total_rows;
        $config['per_page'] = $per_page; 
        $config['uri_segment'] = '5';
        $config['num_links'] = 5;
        $config['suffix'] = $data['query_string'];
        $this->pagination->initialize($config); 


        $this->template->view('admin/entries/entries', $data);
	}

    // ------------------------------------------------------------------------

    /*
     * Edit
     *
     * Add and edit entries

     * @return void
     */
    function edit()
    {
        // Init
        $data = array();
        $data['edit_mode'] = $edit_mode = FALSE;
        $data['breadcrumb'] = set_crumbs(array('content/entries' => 'Entries', current_url() => 'Entry Edit'));

        $data['content_type_id'] = $content_type_id = $this->uri->segment(5);
        $data['entry_id'] = $entry_id = $this->uri->segment(6);
        $data['revision_id'] = $revision_id = $this->uri->segment(7);

        // Get entry
        $this->load->model('users/users_model');
        $this->load->model('entries_model');
        $this->load->model('content_types_model');

        // Used for content types dropdown
        $Content_types_model = new Content_types_model();

        // If user not a super admin check if user's group is allowed access
        if ($this->Group_session->type != SUPER_ADMIN)
        {
            $this->content_types_model
                ->group_start()
                ->where('restrict_admin_access', 0)
                ->or_where_related('admin_groups', 'group_id', $this->Group_session->id)
                ->group_end();

            $this->entries_model
                ->group_start()
                ->where('restrict_admin_access', 0)
                ->or_where_related('content_types/admin_groups', 'group_id', $this->Group_session->id)
                ->group_end();

            // Only get Content Types user has access to for dropdown
            $Content_types_model->group_start()
                ->where('restrict_admin_access', 0)
                ->or_where_related('admin_groups', 'group_id', $this->Group_session->id)
                ->group_end();

        }

        $data['Entry'] = $Entry = $this->entries_model->get_by_id($entry_id);
        $data['Content_type'] = $Content_type = $this->content_types_model->get_by_id($content_type_id);

        // Load content fields library
        $config['Entry'] = $Entry;
        $config['content_type_id'] = $content_type_id;

        $this->load->add_package_path(APPPATH . 'modules/content/content_fields');
        $Content_fields = $this->load->library('content_fields');
        $Content_fields->initialize($config);

        // Check if versioning is enabled and whether a revision is loaded
        if ($Content_type->enable_versioning && is_numeric($revision_id))
        {
            $Revision = new Revisions_model(); 
            $Revision->get_by_id($revision_id);

            if ($Revision->exists())
            {
                $revision_data = @unserialize($Revision->revision_data);

                // Update Entry and content fields from revision
                // Entries data gets queiried in the content_fields library initialize
                if (is_array($revision_data))
                {
                    $Entry->from_array($revision_data);
                    $Content_fields->from_array($revision_data);
                }
            }
        }

        // Get content types for the setting's
        // content type dropdown
        $Change_content_types = $Content_types_model->where('id !=', $content_type_id)->order_by('title')->get();
        $data['change_content_types'] = array('' => '');

        foreach($Change_content_types as $Change_content_type)
        {
            $entries_count = $Change_content_type->entries->count();

            // Only add the content type to the Add Entry dropdown if it has not reached the
            // limit of entries allowed. An empty entries_allowed is unlimited
            if ($Change_content_type->entries_allowed == '' ||  $entries_count < $Change_content_type->entries_allowed || ($entries_count == 0 && $Change_content_type->entries_allowed > 0))
            {
                $data['change_content_types'][$Change_content_type->id] = $Change_content_type->title;
            }
        }

        // Get Admins and Super Admins for the setting's
        // author dropdown
        $Users = $this->users_model->where_in_related('groups', 'type', array(SUPER_ADMIN, ADMINISTRATOR))->order_by('first_name')->get();
        $data['authors'] = array('' => '');
        foreach ($Users as $User)
        {
            $data['authors'][$User->id] = $User->full_name();
        }

        // Validate that the content type exists
        if ( ! $Content_type->exists())
        {
            return show_404();
        }

        if ($Entry->exists())
        {
            // Check that url content_type_id and entry content_type_id match
            if ($Entry->content_type_id != $content_type_id && ! $revision_id)
            {
                return show_404();
            }

            $data['edit_mode'] = $edit_mode = TRUE;
        }

        // Build categories tree if content type has a category group assigned
        if ($Content_type->category_group_id)
        {
            $this->load->library('categories_library');
            $config['id'] = $Content_type->category_group_id;
            $config['admin_entries_categories'] = TRUE;
            $config['populate'] = option_array_value($Entry->categories->get(), 'id', 'id');

            $data['categories_tree'] = $this->categories_library->list_categories($config);
            $Entry->categories->get();
        }

        // Form Validation Rules
        if ($edit_mode)
        {
            $this->form_validation->set_rules('slug', 'URL', 'trim|max_length[255]|callback_unique_slug_check[' . addslashes($Entry->slug) . ']');
        }
        else
        {
            $this->form_validation->set_rules('slug', 'URL', 'trim|max_length[255]|callback_unique_slug_check');
        }

        $this->form_validation->set_rules('meta_title', 'Meta Title', 'trim');
        $this->form_validation->set_rules('meta_description', 'Meta Description', 'trim');
        $this->form_validation->set_rules('meta_keywords', 'Meta Keywords', 'trim');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('title', 'Entry Title', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('created_date', 'Date Created', 'trim|required');

        // Validate url title if content type has a dynamic route
        if ($Content_type->dynamic_route != '')
        {
            $this->form_validation->set_rules('url_title', 'URL Title', 'trim|required|alpha_dash|max_length[100]|is_unique[entries.url_title' . (($edit_mode) ? '.id.' . $Entry->id : '') . ']');
        }

        // Get content fields html
        $field_validation = $Content_fields->validate();

        // Validation and process form
        if ($this->form_validation->run() == TRUE && $field_validation)
        {
            // Populate from post and prep for insert / update
            $Entry->from_array($this->input->post());
            $Entry->modified_date = date('Y-m-d H:i:s');
            $Entry->created_date = date('Y-m-d H:i:s', strtotime($this->input->post('created_date')));
            $Entry->slug = ($this->input->post('slug') != '') ? $this->input->post('slug') : NULL;
            $Entry->url_title = ($this->input->post('url_title') != '') ? $this->input->post('url_title') : NULL;
            $Entry->meta_title = ($this->input->post('meta_title') != '') ? $this->input->post('meta_title') : NULL;
            $Entry->meta_description = ($this->input->post('meta_description') != '') ? $this->input->post('meta_description') : NULL;
            $Entry->meta_keywords = ($this->input->post('meta_keywords') != '') ? $this->input->post('meta_keywords') : NULL;
            $Entry->content_type_id = $content_type_id;
            $Entry->author_id = ($this->input->post('author_id') != '') ? $this->input->post('author_id') : NULL;

            // Ensure the id wasn't overwritten by an id in the post
            if ($edit_mode)
            {
                $Entry->id = $entry_id;
            }

            $Entry->save();

            // Save field data to entries_data
            $Content_fields->from_array($this->input->post());
            $Content_fields->save();

            // Add Revision if versioning enabled
            if ($Content_type->enable_versioning)
            {
                // Delete old revsions so that not to exceed max revisions setting
                $Revision = new Revisions_model();
                $Revision->where_related('revision_resource_types', 'key_name', 'ENTRY')
                    ->where('resource_id', $entry_id)
                    ->order_by('id', 'desc')
                    ->limit(25, $Content_type->max_revisions - 1)
                    ->get()
                    ->delete_all();
                    
                // Serialize and save post data to entry revisions table
                $User = $this->secure->get_user_session();
                $Revision = new Revisions_model();
                $Revision->resource_id = $Entry->id;
                $Revision->revision_resource_type_id = Revision_resource_types_model::ENTRY;
                $Revision->content_type_id = $Entry->content_type_id;
                $Revision->author_id = $User->id;
                $Revision->author_name = $User->first_name . ' ' . $User->last_name;
                $Revision->revision_date = date('Y-m-d H:i:s');
                $Revision->revision_data = serialize($this->input->post());
                $Revision->save();
            }

            // Assign entry to selected categories
            if ($Content_type->category_group_id)
            {
                $this->load->model('categories_entries_model');

                $Categories_entries = new Categories_entries_model();
                $categories_post = (is_array($this->input->post('categories'))) ? $this->input->post('categories') : array();

                // Delete all of the entries categories that are not in the posted array
                $Categories_entries->where('entry_id', $Entry->id);
                if ( ! empty($categories_post))
                {
                    $Categories_entries->where_not_in('category_id', $categories_post);
                }
                $Categories_entries->get()->delete_all();

                // Check if categories posted already exist in the relationship table
                // If not add them
                foreach($categories_post as $category_id)
                {
                    $Categories_entries = new Categories_entries_model();
                    $Categories_entries->where('category_id', $category_id)->where('entry_id', $Entry->id)->get();

                    if ( ! $Categories_entries->exists())
                    {
                        $Categories_entries->category_id = $category_id;
                        $Categories_entries->entry_id = $entry_id;
                        $Categories_entries->save();
                    }
                }
            }

            // Clear cache so updates will show on next page load
            $this->load->library('cache');
            $this->cache->delete_all('entries');

            // Clear navigation cache so updates will show on next page load
            $this->load->library('navigations/navigations_library');
            $this->navigations_library->clear_cache();

            // Set a success message
            $this->session->set_flashdata('message', '<p class="success">Changes Saved.</p>');

            // Deteremine where to redirect user
            if ($this->input->post('save_exit'))
            {
                redirect(ADMIN_PATH . "/content/entries");
            }
            else
            {
                redirect(ADMIN_PATH . "/content/entries/edit/" . $Entry->content_type_id . "/" . $Entry->id);
            }
        }


        $_SESSION['KCFINDER'] = array();
        $_SESSION['KCFINDER']['disabled'] = false;
        $_SESSION['isLoggedIn'] = true;

        // Field form needs to be built after running form_validation->run()
        $data['Fields'] = $Content_fields->form();

        $this->template->view('admin/entries/edit', $data);
    }

    // ------------------------------------------------------------------------

    /*
     * Delete
     *
     * Delete entries and data associated to it

     * @return void
     */
    function delete()
    {
        $this->load->helper('file');
        $this->load->model('entries_model');

        if ($this->input->post('selected'))
        {
            $selected = $this->input->post('selected');
        }
        else
        {
            $selected = (array) $this->uri->segment(5);
        }

        $Entries = new Entries_model();
        $Entries->where_in('id', $selected)->get();

        if ($Entries->exists())
        {
            $message = '';
            $entries_deleted = FALSE;
            $entries_required = FALSE;
            $this->load->model('navigations/navigation_items_model');

            foreach($Entries as $Entry)
            {
                if ($Entry->id == $this->settings->content_module->site_homepage)
                {
                    $message .= '<p class="error">Entry ' . $Entry->title . ' (#' . $Entry->id . ') is set as the site homepage and cannot be deleted.</p>';
                }
                else if ($Entry->id == $this->settings->content_module->custom_404)
                {
                    $message .= '<p class="error">Entry ' . $Entry->title . ' (#' . $Entry->id . ') is set as the custom 404 and cannot be deleted.</p>';
                }
                else if ($Entry->required)
                {
                    $message .= '<p class="error">Entry ' . $Entry->title . ' (#' . $Entry->id . ') is required by the system and cannot be deleted.</p>';
                }
                else
                {
                    // Remove the entry from navigations
                    $Navigation_items = new Navigation_items_model();
                    $Navigation_items->where('entry_id', $Entry->id)->get();
                    $Navigation_items->delete_all();

                    $Entries_data = $Entry->entries_data->get();
                    $Entries_data->delete_all();

                    $Entry_revisions = $Entry->get_entry_revisions();
                    $Entry_revisions->delete_all();

                    $Entry->delete();
                    $entries_deleted = TRUE; 
                }
            }

            if ($entries_deleted)
            {
                // Clear cache so updates will show on next entry load
                $this->load->library('cache');
                $this->cache->delete_all('entries');

                // Clear navigation cache so updates will show on next page load
                $this->load->library('navigations/navigations_library');
                $this->navigations_library->clear_cache();

                $message .= '<p class="success">The selected items were successfully deleted.</p>';
            }

            $this->session->set_flashdata('message', $message);
        }

        redirect(ADMIN_PATH . '/content/entries');
    }

    // ------------------------------------------------------------------------

    /*
     * Links
     *
     * Used by TinyMCE to build a list of of pages and get their URL

     * @return void
     */
	function links() {
        header('Content-type: text/javascript');

        $Entries = $this->load->model('content/entries_model');
        $Entries->where('status', 'published')
            ->where('slug !=', 'NULL')
            ->or_where('id =', $this->settings->content_module->site_homepage)
            ->order_by('title')
            ->get();

        $output = "var tinyMCELinkList = new Array(";

        foreach($Entries as $Entry)
        {
            $output .= "['$Entry->title', '{{ content:entry_url entry_id=\'$Entry->id\' }}'],";
        }

        $output = rtrim($output, ',');

        $output .= ");";

        echo $output;
	}

    // ------------------------------------------------------------------------

    /*
     * CSS
     *
     * Called by CKEditor and TinyMCE for custom styles

     * @return void
     */
    function css()
    {
        // No long use the entry_id variable but maybe some day
        $entry_id = $this->uri->segment(5);

        $css = @file_get_contents(base_url('themes/' . $this->settings->theme . '/' . trim($this->settings->editor_stylesheet, '/'))) . "\n";

        header('Content-type: text/css');

        echo $css;
    }

    // ------------------------------------------------------------------------

    /*
     * Unique Slug Check
     *
     * Used to validate that the slug is a valid URL 
     * and is unique in the database

     * @return bool or string
     */
    function unique_slug_check($slug, $current_slug = '')
    {
        $slug = trim($slug, '/');

        $regex = "(([a-zA-Z0-9+\$_-]\.?)+)*\/?"; // Path
        $regex .= "(\?[a-zA-Z+&\$_.-][a-zA-Z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
        $regex .= "(#[a-zA-Z_.-][a-zA-Z0-9+\$_.-]*)?"; // Anchor 

        if (preg_match("/^$regex$/", $slug))
        {
            $Entries = new Entries_model();
            $Entries->get_by_slug($slug);

            if ($Entries->exists() && $slug != stripslashes($current_slug))
            {
                $this->form_validation->set_message('unique_slug_check', 'This %s provided is already in use.');
                return FALSE;
            }
            else
            {
                return $slug;
            }
        }
        else
        {
            $this->form_validation->set_message('unique_slug_check', 'The %s provided is not valid.');
            return FALSE;
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Create Thumb
     *
     * Called by AJAX to create thumbnails of selected images
     * 
     * @return void
     */
    function create_thumb()
    {
        if ( ! is_ajax())
        {
            return show_404();
        }

        if ($this->input->post('image_path'))
        {
            echo image_thumb($this->input->post('image_path'), 150, 150, FALSE, array('no_image_image' => ADMIN_NO_IMAGE));
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Save Inline Content
     *
     * Called by AJAX to save inline content elements
     * 
     * @return void
     */
    function save_inline_content()
    {
        if ( ! is_ajax())
        {
            return show_404();
        }

        $this->load->model('entries_model');
        $this->load->model('revisions_model');
        $this->load->add_package_path(APPPATH . 'modules/content/content_fields');
        $response['status'] = 'success';
        $data = array();

        foreach ($_POST as $key => $content)
        {
            // Preg match the entry id and the field id from the html element's id attribute
            if (preg_match("/cc_field_(\d+)_(\d+|title)/", $key, $matches))
            {
               $entry_id = $matches[1];
               $field_id = $matches[2];

               // Build a new data array sorted by entry
               $data[$entry_id]['field_id_' . $field_id] = $content;
            }
        }

        foreach ($data as $entry_id => $fields)
        {
            $Entry = new Entries_model();

            // If user not a super admin check if user's group is allowed access
            if ($this->Group_session->type != SUPER_ADMIN)
            {
                $Entry->group_start()
                    ->where('restrict_admin_access', 0)
                    ->or_where_related('content_types/admin_groups', 'group_id', $this->Group_session->id)
                    ->group_end();
            }

            $Entry->get_by_id($entry_id);

            // Either entry doesn't exist or user doesn't have permission to it so skip it
            if ( ! $Entry->exists())
            {
                continue;
            }

            $Content_type = $Entry->content_types->get();

            // Load content fields library
            $config['Entry'] = $Entry;
            $config['content_type_id'] = $Entry->content_type_id;

            $Content_fields = $this->load->library('content_fields');
            $Content_fields->initialize($config);

            // Get content fields html
            $field_validation = $Content_fields->inline_validate();

            // Validation and process form
            if ($this->form_validation->run() == TRUE && $field_validation)
            {
                if (isset($fields['field_id_title']))
                {
                    $Entry->title = $fields['field_id_title'];
                }

                $Entry->modified_date = date('Y-m-d H:i:s');
                $Entry->save();

                $Content_fields->from_array($fields);
                $Content_fields->save();

                // Add Revision if versioing enabled
                if ($Content_type->enable_versioning)
                {
                    // Delete old revsions so that not to exceed max revisions setting
                    $Revision = new Revisions_model();
                    $Revision->where_related('revision_resource_types', 'key_name', 'ENTRY')
                        ->where('resource_id', $entry_id)
                        ->order_by('id', 'desc')
                        ->limit(25, $Content_type->max_revisions - 1)
                        ->get()
                        ->delete_all();
                        
                    // Serialize and save post data to entry revisions table
                    $User = $this->secure->get_user_session();
                    $Revision = new Revisions_model();
                    $Revision->resource_id = $Entry->id;
                    $Revision->revision_resource_type_id = Revision_resource_types_model::ENTRY;
                    $Revision->content_type_id = $Entry->content_type_id;
                    $Revision->author_id = $User->id;
                    $Revision->author_name = $User->first_name . ' ' . $User->last_name;
                    $Revision->revision_date = date('Y-m-d H:i:s');
                    $Revision->revision_data = serialize($this->input->post());
                    $Revision->save();
                }
            }
        }

        // Check if there were any validation errors
        if (validation_errors())
        {
            $validation_errors = validation_errors("-", " ");
            $response['status'] = 'error';
            $response['message'] = $validation_errors;
        }

        // Clear cache so updates will show on next page load
        $this->load->library('cache');
        $this->cache->delete_all('entries');

        // Clear navigation cache so updates will show on next page load
        $this->load->library('navigations/navigations_library');
        $this->navigations_library->clear_cache();

        echo json_encode($response);
    }

    // ------------------------------------------------------------------------

    /*
     * Pre Save Output
     *
     * Called by AJAX to return processed output content from its
     * content field type before it has been saved to the db
     * 
     * @return string
     */
    function pre_save_output()
    {
        if ( ! is_ajax())
        {
            return show_404();
        }

        // Init
        $this->load->model('entries_model');
        $this->load->model('content_fields_model');
        $response = array();

        $editable_id = $this->input->post('editable_id');
        $content = $this->input->post('content');

        // Preg match the entry id and the field id from the html element's id attribute
        if (preg_match("/cc_field_(\d+)_(\d+)/", $editable_id, $matches))
        {
            $entry_id = $matches[1];
            $field_id = $matches[2];
        }
        else
        {
            $response['status'] = 'error';
            $response['message'] = 'Unable to parse the entry id and field id.';
            echo json_encode($response);
        }

        $Entry = new Entries_model();
        $Entry->get_by_id($entry_id);

        if ( ! $Entry->exists())
        {
            $response['status'] = 'error';
            $response['message'] = 'The entry id provided does not exist.';
            echo json_encode($response);
        }

        $Field = new Content_fields_model();
        $Field->order_by('sort', 'ASC')
            ->include_related('content_field_types', array('model_name'))
            ->get_by_id($field_id);

        if ( ! $Field->exists())
        {
            $response['status'] = 'error';
            $response['message'] = 'The field id provided does not exist.';
            echo json_encode($response);
        }

        $Content_object = new stdClass();
        $Content_object->{'field_id_' . $Field->id} = $content;

        $Field_type = Field_type::factory($Field->content_field_types_model_name, $Field, $Entry, $Content_object);
        $output = $Field_type->output();

        $response['status'] = 'success';
        $response['content'] = $output;
        echo json_encode($response);
    }
}

