<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content_fields_model extends DataMapper
{	
    public $table = "content_fields";
    public $has_one = array(
        'content_types' => array(
            'class' => 'content_types_model',
            'other_field' => 'content_fields',
            'join_self_as' => 'content_field',
            'join_other_as' => 'content_type',
        ),
        'content_field_types' => array(
            'class' => 'content_field_types_model',
            'other_field' => 'content_fields',
            'join_other_as' => 'content_field_type',
        ),
    );

    /*
     * Add Entries Column
     *
     * Adds a field column to the entires_data table
     *
     * @param string
     * @return void
     */
    public function save_entries_column($datatype = 'TEXT')
    {
        if ($this->exists())
        {
            $column_query = $this->db->query("Show columns from `entries_data` like 'field_id_" . $this->id . "'");

            if ($column_query->num_rows() == 0)
            {
                $sql = "ALTER TABLE `entries_data` ADD `field_id_" . $this->id . "` $datatype NULL";
                $this->db->query($sql);
            }
            else
            {
                $sql = "ALTER TABLE `entries_data` MODIFY `field_id_" . $this->id . "` $datatype NULL";
                $this->db->query($sql);
            }

            $CI =& get_instance();
            $CI->load->library('cache');
            $CI->cache->delete_all('datamapper');
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Drop Entries Column
     *
     * Drops field column from entires_data table
     *
     * @param int
     * @return void
     */
    public function drop_entries_column()
    {
        if ($this->exists())
        {
            $column_query = $this->db->query("Show columns from `entries_data` like 'field_id_" . $this->id . "'");

            if ($column_query->num_rows() > 0)
            {
                $sql = "ALTER TABLE `entries_data` DROP `field_id_" . $this->id ."`";
                $this->db->query($sql);
            }

            $CI =& get_instance();
            $CI->load->library('cache');
            $CI->cache->delete_all('datamapper');
        }
    }
}
