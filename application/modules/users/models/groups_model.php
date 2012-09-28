<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Groups_model extends DataMapper
{	
    public $table = "groups";
    public $has_many = array(
        'users' => array(
            'class' => 'users_model',
            'other_field' => 'groups',
            'join_self_as' => 'group',
            'model_path' => 'application/modules/users',
        ),
    );
}
