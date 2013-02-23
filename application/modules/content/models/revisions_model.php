<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Revisions_model extends DataMapper
{   
    public $table = "revisions";
    public $has_one = array(
        'revision_resource_types' => array(
            'class' => 'revision_resource_types_model',
            'other_field' => 'revisions',
            'join_other_as' => 'revision_resource_type',
        ),
    );
}
