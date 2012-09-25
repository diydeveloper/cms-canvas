<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entries_cache_model extends CI_Model
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
            $CI =& get_instance();
            $CI->load->library('parser');

            // Build entry data using the entries library
            $data = $this->get_content_array();
            
            // Parse the content with data
            $return = $CI->parser->parse_string($this->content_types->layout, $data, TRUE);

            return $return;
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Get Content Array
     *
     * Build array of content fields and values
     *
     * @return array
     */
    function get_content_array()
    {
        if (isset($this->content_array) && is_array($this->content_array))
        {
            return $this->content_array;
        }
        else
        {
            $this->content_array = array();

            // Format object for build
            $Object = new stdClass();

            $Object->id = $this->id;
            $Object->slug = $this->slug;
            $Object->title = $this->title;
            $Object->url_title = $this->url_title;
            $Object->required = $this->required;
            $Object->content_type_id = $this->content_type_id;
            $Object->status = $this->status;
            $Object->meta_title = $this->meta_title;
            $Object->meta_description = $this->meta_description;
            $Object->meta_keywords = $this->meta_keywords;
            $Object->created_date = $this->created_date;
            $Object->modified_date = $this->modified_date;
            $Object->author_id = $this->author_id;

            // Build content array
            $CI =& get_instance();
            $CI->load->library('entries_library');
            $Entries_library = new Entries_library();
            $Entries_library->content_fields = $this->content_fields;
            $Entries_library->_content = $this->content_types->layout;

            foreach ($this->entry_data as $key => $value)
            {
                $Object->$key = $value;
            }

            $Object->dynamic_route = $this->content_types->dynamic_route;
            $Object->layout = $this->content_types->layout;
            $Object->short_name = $this->content_types->short_name;

            $Entries_library->build_entry_data($Object);
            $this->content_array = current($Entries_library->entries);

            return $this->content_array;
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Cacheable Page Slugs
     *
     * Gets all page uris from the entries table
     * Uses standard CI Active Record to minimize execution times and cache file sizes
     *
     * @return array
     */
    function cacheable_page_slugs()
    {
        $query = $this->db->select('id, slug')
            ->from('entries')
            ->where('slug !=', 'NULL')
            ->order_by('slug', 'desc')
            ->get();

        $slugs = array();

        foreach ($query->result() as $Result)
        {
            $slugs[$Result->slug] = $Result->id;
        }

        return $slugs;
    }

    // ------------------------------------------------------------------------

    /*
     * Cacheable Get By
     *
     * Gets entry by slug and queries fields and values
     * Uses standard CI Active Record to minimize execution times and cache file sizes
     *
     * @param string
     * @return object
     */
    public function cacheable_get_by($arguments)
    {
        $field = key($arguments);
        $value = current($arguments);

        // Build arguments into uri string if searching by slug
        if ($field == 'slug')
        {
            if ( ! is_array($value))
            {
                $value = (array) $value;
            }

            $value = implode('/', $value);
        }

        $CI =& get_instance();

        $CI->load->library('Standard_result');

        $Entry = new Entries_cache_model();

        $query = $CI->db->from('entries')
            ->where($field, $value)
            ->get();

        if ($query->num_rows() > 0)
        {
            $Entry = $query->row(0, 'entries_cache_model');

            // Get Content Type
            $query = $CI->db->from('content_types')
                ->where('content_types.id', $Entry->content_type_id)
                ->get();

            $Entry->content_types = $query->row(0, 'standard_result');

            // Get Content Fields
            $query = $CI->db
                ->select('content_fields.*, content_field_types.model_name')
                ->from('content_fields')
                ->join('content_field_types', 'content_fields.content_field_type_id = content_field_types.id')
                ->where('content_fields.content_type_id', $Entry->content_type_id)
                ->get();

            $Entry->content_fields = $query->result();

            // Get Entry Data
            $select = 'id, entry_id';

            foreach($Entry->content_fields as $Field)
            {
                $select .= ', field_id_' . $Field->id;
            }

            $query = $CI->db->select($select)
                ->from('entries_data')
                ->where('entries_data.entry_id', $Entry->id)
                ->get();

            $Entry->entry_data = $query->row(0, 'standard_result');
        }
        else
        {
            $Entry->content_types = new Standard_result();
            $Entry->content_fields = array();
            $Entry->entry_data = new Standard_result();
        }

        return $Entry;
    }
}
