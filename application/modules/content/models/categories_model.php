<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories_model extends DataMapper
{	
    public $table = "categories";
    public $has_one = array(
        'category_groups' => array(
            'class' => 'category_groups_model',
            'other_field' => 'categories',
            'join_other_as' => 'category_group',
        ),
    );

    public $has_many = array(
        'entries' => array(
            'class' => 'entries_model',
            'other_field' => 'categories',
            'join_self_as' => 'category',
            'join_other_as' => 'entry',
            'join_table' => 'categories_entries',
        ),
    );
}
