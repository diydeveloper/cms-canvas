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
        'content_type_revisions' => array(
            'class' => 'content_type_revisions_model',
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
     * Adds the content type data to the content type revisions table
     *
     * @return void
     */
    public function add_revision()
    {
        $CI =& get_instance();
        $CI->load->model('content_type_revisions_model');

        $_POST['layout'] = (isset($_POST['layout'])) ? $CI->input->post('layout') : '';
        $_POST['page_head'] = (isset($_POST['page_head'])) ? $CI->input->post('page_head') : '';

        // Delete old revsions so that not to exceed 5 revisions
        $Revision = new Content_type_revisions_model();
        $Revision->where('content_type_id', $this->id)
            ->order_by('id', 'desc')
            ->limit(25, 5 - 1)
            ->get()
            ->delete_all();
            
        // Serialize and save post data to content type revisions table
        $User = $CI->secure->get_user_session();
        $Revision = new Content_type_revisions_model();
        $Revision->content_type_id = $this->id;
        $Revision->author_id = $User->id;
        $Revision->author_name = $User->first_name . ' ' . $User->last_name;
        $Revision->revision_date = date('Y-m-d H:i:s');
        $Revision->revision_data = serialize($CI->input->post());
        $Revision->save();
    }
}
