<?php 

namespace CmsCanvas\Admin;

use Route, Request, Config;

class Admin {

    /**
     * Generates an admin url for the provided path
     *
     * @param string
     * @return string
     */
    public function url($path = null)
    {
        return url($this->getUrlPrefix().'/'.trim($path, '/'));
    }

    /**
     * Returns the admin url prefix
     *
     * @return string
     */
    public function getUrlPrefix()
    {
        return Config::get('cmscanvas::admin.url_prefix');
    }

    /**
     * Checks if current uri segments exist in array of uri strings
     *
     * @param string
     * @return bool
     */
    protected function isPermitted($uri)
    {
        // @TODO
        return true;
    }   

    /**
     * Generates the html to build the admin navigation 
     * showing only links that the admin is permitted
     *
     * @param array
     * @param int
     * @return string
     */
    public function navigation($items, $depth = 1)
    {
        $listItem = '<ul>';

        foreach($items as $item) {
            $item['route'] = trim($item['route'], '/');

            if ($this->canShowListItem($item)) {
                $listItem .= '<li'.(($depth == 1 && $this->isListItemSelected($item)) ? ' class="selected"' : '')
                    . ((isset($item['id'])) ? ' id="' . $item['id'].'"' : '').'>';
                $listItem .= '<a href="'.($this->isPermitted($this->getUrlPrefix().'/'.$item['route']) ? url($this->getUrlPrefix().'/'.$item['route']) : 'javascript:void(0)')
                    . '"'.(($depth == 1) ? ' class="top"' : '').'>';
                $listItem .= $item['title'].(($depth == 1 && ! empty($item['children'])) ?'<span class="down_arrow_small"></span>' : '');
                $listItem .= '</a>';

                if (! empty($item['children'])) {
                    $listItem .= $this->navigation($item['children'], $depth + 1);
                }

                $listItem .= '</li>';
            }
        }

        $listItem .= '</ul>';

        return $listItem;
    }   

    /**
     * Called by nav to determine if admin is permitted to navigation list item
     *
     * @param  array
     * @param  int
     * @return bool
     */
    protected function canShowListItem($item, $depth = 1)
    {
        if ($this->isPermitted($this->getUrlPrefix().'/'.trim($item['route'], '/'))) {
            return true;
        }

        if (! empty($item['children'])) {
            foreach ($item['children'] as $item) {
                if ($this->canShowListItem($item, ++$depth)) {
                    return true;
                }
            }
        }

        return false;
    }   

    /**
     * Called by nav to determine if current page 
     * is in the current trail of current nav item
     *
     * @param  array
     * @param  int
     * @return bool
     */
    protected function isListItemSelected($item, $depth = 1)
    {
        if (!empty($item['associated_route_names'])) {
            foreach ($item['associated_route_names'] as $pattern) {
                if (fnmatch($pattern, Route::currentRouteName())) {
                    return true;
                }
            }
        }

        if (! empty($item['children'])) {
            foreach ($item['children'] as $child) {
                if ($this->isListItemSelected($child, ++$depth)) {
                    return true;
                }
            }
        }

        return false;
    }   

}
