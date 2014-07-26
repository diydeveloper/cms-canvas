<?php namespace CmsCanvas\Admin;

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
        // $permissions = unserialize(Auth::user()->group->permissions);
        // $permissions = isset($permissions['access']) ? $permissions['access'] : array();
        // $access_options = unserialize(ADMIN_ACCESS_OPTIONS);

        // if (( ! in_uri($permissions, $uri) && Auth::user()->group->type == ADMINISTRATOR)  && in_uri($access_options, $uri, true))
        // {
        //     return false;
        // }

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
    public function nav($nav, $depth = 1)
    {
        $listItem = '<ul>';

        foreach($nav as $item)
        {
            $item['url'] = trim($item['url'], '/');

            if ($this->showNavLi($item) && empty($item['hidden']))
            {
                $listItem .= '<li' . (($depth == 1 && $this->isNavLiSelected($item)) ? ' class="selected"' : '') . ((isset($item['id'])) ? ' id="' . $item['id'] . '"' : '') . '>';
                $listItem .= '<a href="' . ($this->isPermitted(Config::get('cmscanvas::admin.url_prefix') . '/' . $item['url']) ? url(Config::get('cmscanvas::admin.url_prefix') . '/' . $item['url']) : 'javascript:void(0)') . '"' . (($depth == 1) ? ' class="top"' : '') . '>' . $item['title'] . (($depth == 1 && ! empty($item['sub'])) ?'<span class="down_arrow_small"></span>' : '') . '</a>';

                if ( ! empty($item['sub']))
                {
                    $listItem .= $this->nav($item['sub'], $depth + 1);
                }

                $listItem .= '</li>';
            }
        }

        $listItem .= '</ul>';

        return $listItem;
    }   

    /**
     * Called by nav to determine if admin is permitted to nav item
     *
     * @param array
     * @param int
     * @return bool
     */
    protected function showNavLi($item, $depth = 1)
    {
        if ($this->isPermitted(Config::get('cmscanvas::admin.url_prefix') . '/' . trim($item['url'], '/')) && empty($item['hidden']))
        {
            return true;
        }

        if ( ! empty($item['sub']) && empty($item['hidden']))
        {
            foreach ($item['sub'] as $item)
            {
                if ($this->showNavLi($item, $depth + 1))
                {
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
     * @param array
     * @param int
     * @return bool
     */
    protected function isNavLiSelected($item, $depth = 1)
    {
        $uriSegments = explode('/', $item['url']);
        $segmentMatch = true;

        $i = 2;
        foreach($uriSegments as $segment)
        {
            if (Request::segment($i) != $segment)
            {
                $segmentMatch = false;

                break;
            }

            $i++;
        }

        if ($segmentMatch)
        {
            return true;
        }

        if ( ! empty($item['sub']))
        {
            foreach ($item['sub'] as $item)
            {
                if ($this->isNavLiSelected($item, $depth + 1))
                {
                    return true;
                }
            }
        }

        return false;
    }   

}
