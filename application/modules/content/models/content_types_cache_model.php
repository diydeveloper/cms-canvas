<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content_types_cache_model extends CI_Model
{	
    /*
     * Exists
     *
     * Checks to that query result found
     *
     * @return bool
     */
    function exists()
    {
        if (isset($this->id))
        {
            return TRUE;
        }

        return FALSE;
    }

    // ------------------------------------------------------------------------

    /*
     * Build Content
     *
     * Parse and piece together output for CMS page
     *
     * @param array
     * @return string
     */
    function build_content($data = array())
    {
        if ($this->exists())
        {
            $CI = get_instance();
            $CI->load->library('parser');

            $data['title'] = $this->title;

            $return = $CI->parser->parse_string($this->layout, $data, TRUE);

            return $return;
        }
    }

    // ------------------------------------------------------------------------

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
            ->from('content_types')
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

        $query = $this->db->from('content_types')
            ->where('id', current($id))
            ->get();

        return $query->row(0, 'content_types_cache_model');
    }

    // ------------------------------------------------------------------------

    /*
     * Cacheable cacheable_content_fields
     *
     * Gets all content fields and builds array by content type of fields
     * Uses standard CI Active Record to minimize execution times and cache file sizes
     *
     * @param int
     * @return object
     */
    function cacheable_content_fields()
    {
        // Get Content Fields
        $this->db->select('content_fields.*, content_types.short_name, content_field_types.model_name')
            ->from('content_fields')
            ->join('content_types', 'content_fields.content_type_id = content_types.id')
            ->join('content_field_types', 'content_fields.content_field_type_id = content_field_types.id');

        $Query = $this->db->get();

        $content_fields = array();

        foreach ($Query->result() as $Content_field)
        {
            $content_fields[$Content_field->short_name][] = $Content_field;
        }

        return $content_fields;
    }
}
