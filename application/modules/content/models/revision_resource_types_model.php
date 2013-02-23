<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Revision_resource_types_model extends DataMapper
{   
    const ENTRY = 1;
    const CONTENT_TYPE = 2;
    const SNIPPET = 3;

    public $table = "revision_resource_types";
    public $has_many = array(
        'revisions' => array(
            'class' => 'revisions_model',
            'other_field' => 'revision_types',
            'join_self_as' => 'revision_resource_type',
        ),
    );
}
