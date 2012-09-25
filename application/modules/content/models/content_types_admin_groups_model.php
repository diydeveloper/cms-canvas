<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content_types_admin_groups_model extends DataMapper
{	
    public $table = "content_types_admin_groups";

    public $has_one = array(
        'content_types' => array(
            'class' => 'content_types_model',
            'other_field' => 'admin_groups',
            'join_other_as' => 'content_type',
        ),
    );
}
