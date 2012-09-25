<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content_types_model extends DataMapper
{	
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
}
