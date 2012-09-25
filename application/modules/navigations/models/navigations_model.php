<?php
class Navigations_model extends DataMapper
{	
    public $table = "navigations";

    public $has_many = array(
        'navigation_items' => array(
            'class' => 'navigation_items_model',
            'other_field' => 'navigations',
            'join_self_as' => 'navigation',
            'join_other_as' => 'navigation_item',
            'model_path' => 'application/modules/navigations',
        ),
    );
}
