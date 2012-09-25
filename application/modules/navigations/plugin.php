<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Navigations_plugin extends Plugin
{
    public function nav()
    {
        $attributes = $this->attributes();
        $attributes['_content'] = $this->content();

        return nav($this->attribute('nav_id'), $attributes);
    }

    public function breadcrumb()
    {
        $attributes = $this->attributes(); 
        $attributes['_content'] = $this->content();

        return breadcrumb($this->attribute('nav_id'), $attributes);
    }
}

