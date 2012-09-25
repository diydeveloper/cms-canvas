<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category_groups_model extends DataMapper
{	
    public $table = "category_groups";
    public $has_many = array(
        'categories' => array(
            'class' => 'categories_model',
            'other_field' => 'category_groups',
            'join_self_as' => 'category_group',
        ),
    );
}
