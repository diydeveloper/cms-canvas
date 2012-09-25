<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content_field_types_model extends DataMapper
{	
    public $table = "content_field_types";
    public $has_many = array(
        'content_fields' => array(
            'class' => 'content_fields_model',
            'other_field' => 'content_field_types',
            'join_self_as' => 'content_field_type',
        ),
    );
}
