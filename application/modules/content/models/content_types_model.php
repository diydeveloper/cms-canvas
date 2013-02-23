<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content_types_model extends DataMapper
{	
    // Used to cache the current user's permissions to content types for inline editing
    public $has_permission_cache = array();

    public $table = "content_types";
    public $has_many = array(
        'entries' => array(
            'class' => 'entries_model',
            'other_field' => 'content_types',
            'join_self_as' => 'content_type',
            'join_other_as' => 'entry',
        ),
        'content_fields' => array(
            'class' => 'content_fields_model',
            'other_field' => 'content_types',
            'join_self_as' => 'content_type',
            'join_other_as' => 'content_field',
        ),
        'admin_groups' => array(
            'class' => 'content_types_admin_groups_model',
            'other_field' => 'content_types',
            'join_self_as' => 'content_type',
        ),
    );
    
    /*
     * Cacheable Dynamic Routes
     *
     * Gets all dynamic routes from content types table
     * Uses standard CI Active Record to minimize execution times and cache file sizes
     *
     * @return array
     */
    function cacheable_dynamic_routes()
    {
        // Order by descending dynamic_route is important so that child routes
        // will be matched before attempting to match parent level routes
        $query = $this->db->select('id, dynamic_route')
            ->from($this->table)
            ->where('dynamic_route !=', 'NULL')
            ->order_by('dynamic_route', 'desc')
            ->get();

        return $query->result();
    }

    // ------------------------------------------------------------------------

    /*
     * Cacheable Get By Id
     *
     * Gets content type by id
     * Uses standard CI Active Record to minimize execution times and cache file sizes
     *
     * @param int
     * @return object
     */
    function cacheable_get_by_id($id)
    {
        $this->load->model('content_types_cache_model');

        $query = $this->db->from($this->table)
            ->where('id', current($id))
            ->get();

        return $query->row(0, 'content_types_cache_model');
    }

    // ------------------------------------------------------------------------

    /*
     * Add Revision
     *
     * Adds the content type data to the revisions table
     *
     * @return void
     */
    public function add_revision()
    {
        $CI =& get_instance();
        $CI->load->model('revisions_model');

        $_POST['layout'] = (isset($_POST['layout'])) ? $CI->input->post('layout') : '';
        $_POST['page_head'] = (isset($_POST['page_head'])) ? $CI->input->post('page_head') : '';

        // Delete old revsions so that not to exceed 5 revisions
        $Revision = new Revisions_model();
        $Revision->where_related('revision_resource_types', 'key_name', 'CONTENT_TYPE')
            ->where('resource_id', $this->id)
            ->order_by('id', 'desc')
            ->limit(25, 5 - 1)
            ->get()
            ->delete_all();
            
        // Serialize and save post data to content type revisions table
        $User = $CI->secure->get_user_session();
        $Revision = new Revisions_model();
        $Revision->revision_resource_type_id = Revision_resource_types_model::CONTENT_TYPE;
        $Revision->resource_id = $this->id;
        $Revision->author_id = $User->id;
        $Revision->author_name = $User->first_name . ' ' . $User->last_name;
        $Revision->revision_date = date('Y-m-d H:i:s');
        $Revision->revision_data = serialize($CI->input->post());
        $Revision->save();
    }

    // ------------------------------------------------------------------------

    /*
     * Get Revisions
     *
     * Gets past revisions for the content type
     *
     * @return void
     */
    public function get_revisions() {
        $CI =& get_instance();
        $CI->load->model('revisions_model');

        $Revisions = new Revisions_model();
        $Revisions->where_related('revision_resource_types', 'key_name', 'CONTENT_TYPE')
                  ->where('resource_id', $this->id)
                  ->order_by('id', 'desc')
                  ->get();

        return $Revisions;
    }

    // ------------------------------------------------------------------------

    /*
     * Delete Revisions
     *
     * Deletes the current content type's revisions
     *
     * @return void
     */
    public function delete_revisions() {
        $CI =& get_instance();
        $CI->load->model('revisions_model');

        $Revisions = new Revisions_model();
        $Revisions->where_related('revision_resource_types', 'key_name', 'CONTENT_TYPE')
                  ->where('resource_id', $this->id)
                  ->get();

        $Revisions->delete_all();
    }
}
