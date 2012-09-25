<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entry_revisions_model extends DataMapper
{	
    public $table = "entry_revisions";
    public $has_one = array(
        'entries' => array(
            'class' => 'entries_model',
            'other_field' => 'content_types',
            'join_other_as' => 'entry',
        ),
    );
}
