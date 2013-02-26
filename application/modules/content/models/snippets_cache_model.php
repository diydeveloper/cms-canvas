<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Snippets_cache_model extends CI_Model
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
     * Cacheable Get By Short Name
     *
     * Gets snippet by short name
     * Uses standard CI Active Record to minimize execution times and cache file sizes
     *
     * @param string
     * @return object
     */
    function cacheable_get_by_short_name($short_name)
    {
        $this->load->model('snippets_cache_model');

        $query = $this->db->from('snippets')
            ->where('short_name', current($short_name))
            ->get();

        return $query->row(0, 'snippets_cache_model');
    }
}
