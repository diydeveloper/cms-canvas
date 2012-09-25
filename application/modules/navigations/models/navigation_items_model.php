<?php
class Navigation_items_model extends DataMapper
{	
    public $table = "navigation_items";

    public $has_one = array(
        'navigations' => array(
            'class' => 'navigations_model',
            'other_field' => 'navigation_items',
            'join_self_as' => 'navigation_item',
            'join_other_as' => 'navigation',
            'model_path' => 'application/modules/navigations',
        ),
    );
}
