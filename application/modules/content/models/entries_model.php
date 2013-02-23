<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entries_model extends DataMapper
{	
    public $table = "entries";
    public $has_one = array(
        'content_types' => array(
            'class' => 'content_types_model',
            'other_field' => 'entries',
            'join_self_as' => 'entry',
            'join_other_as' => 'content_type',
        ),
    );

    public $has_many = array(
        'entries_data' => array(
            'class' => 'entries_data_model',
            'other_field' => 'entries',
            'join_self_as' => 'entry',
        ),
        'categories' => array(
            'class' => 'categories_model',
            'other_field' => 'entries',
            'join_self_as' => 'entry',
            'join_other_as' => 'category',
            'join_table' => 'categories_entries',
        ),
    );

    public function get_entry_revisions() {
        $CI =& get_instance();
        $CI->load->model('revisions_model');

        $Revisions = new Revisions_model();
        $Revisions->where_related('revision_resource_types', 'key_name', 'ENTRY')
                  ->where('resource_id', $this->id)
                  ->order_by('id', 'desc')
                  ->get();

        return $Revisions;
    }
}
