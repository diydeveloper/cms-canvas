<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entries_data_model extends DataMapper
{	
    public $table = "entries_data";
    public $has_one = array(
        'entries' => array(
            'class' => 'entries_model',
            'other_field' => 'entries_data',
            'join_other_as' => 'entry',
        ),
    );
}
