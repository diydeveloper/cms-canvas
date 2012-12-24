<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content_type_revisions_model extends DataMapper
{	
    public $table = "content_type_revisions";
    public $has_one = array(
        'content_types' => array(
            'class' => 'content_types_model',
            'other_field' => 'content_type_revisions',
            'join_other_as' => 'content_type',
        ),
    );
}