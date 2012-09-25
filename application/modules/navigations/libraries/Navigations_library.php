<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Navigations_library
{
    function __construct($config = array())
    {
        $this->CI =& get_instance();
        $this->CI->load->library('parser');
        $this->CI->load->library('cache');
        $this->clear();
    }

    // ------------------------------------------------------------------------

    /*
     * Clear
     *
     * Resets class variables to default settings
     *
     * @access public
     * @return void
     */
    public function clear()
    {
        $this->tree = array();
        $this->start_node = 0;
        $this->start_nav_on_level_of_current = FALSE;
        $this->start_nav_from_parent = FALSE;
        $this->start_nav_with_kids_of_current = FALSE;
        $this->no_current = FALSE;
        $this->start_nav_from_parent_depth = 1;
        $this->start_x_levels_above_current = 0;
        $this->current_depth = 0;
        $this->tag_id = null;
        $this->class = null;
        $this->subnav_visibility = null;
        $this->id = null;
        $this->admin_nav = FALSE;
        $this->nested = TRUE;
        $this->max_depth = 0;
        $this->backspace = 0;
        $this->_content = '';

        // Breadcrumb only parameters
        $this->breadcrumb_seperator = null;
    }

    // ------------------------------------------------------------------------

    /*
     * Set Config
     *
     * Sets an array of config items to class variables
     *
     * @access protected
     * @param array
     * @return void
     */
    protected function _set_config($config = array())
    {
        foreach ($config as $var => $value)
        {
            $this->$var = $value;
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Initialize
     *
     * Sets config and builds out navigation tree
     *
     * @access public
     * @param array
     * @return void
     */
    public function initialize($config)
    {
        // Reset class data
        $this->clear();

        // Set Config Parameters
        $this->_set_config($config);

        // Build tree
        $this->tree = $this->_get_nav();

        // Mark current and current trail nav items
        if ( ! $this->no_current)
        {
            $this->tree = $this->_set_current($this->tree);
            $this->tree = $this->_set_current_trail($this->tree);
        }

        // Calculate the depth of subset requested
        if ($this->start_nav_on_level_of_current)
        {
            $calculated_depth = $this->current_depth - 1;

            if ($calculated_depth > 0)
            {
                $this->start_nav_from_parent_depth = $calculated_depth;
            }
        }
        elseif ($this->start_x_levels_above_current > 0)
        {
            $calculated_depth = $this->current_depth - $this->start_x_levels_above_current;

            if ($calculated_depth > 0)
            {
                $this->start_nav_from_parent_depth = $calculated_depth;
            }
        }

        // Get a nav subset
        if (($this->start_nav_from_parent) || ($this->start_x_levels_above_current > 0))
        {
            $this->tree = $this->_current_parent_subset($this->tree);
        } 
        elseif ($this->start_nav_on_level_of_current)
        {
            $this->tree = $this->_current_sibling_subset($this->tree);
        } 
        elseif ($this->start_nav_with_kids_of_current)
        {
            $this->tree = $this->_current_kids_subset($this->tree);
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Get Nav
     *
     * Checks and returns a cached navigation, if there is no cached version of the nav it will create one
     *
     * @access protected
     * @param bool
     * @return array
     */
    protected function _get_nav($cache = true)
    {
        $recursive = TRUE;

        if ( ! empty($this->subnav_visibility) && ($this->subnav_visibility != 'show' || $this->subnav_visibility != 'current_trail'))
        {
            $recursive = FALSE;
        }

        if ($cache)
        {
            $tree = $this->_get_cache($this->id, $this->start_node, $recursive);

            if ($tree === FALSE)
            {
                $tree = $this->_build_tree($this->id, $this->start_node, $recursive);
                $this->_set_cache($this->id, $this->start_node, $recursive, $tree);
            }
        }
        else
        {
            $tree = $this->_build_tree($this->id, $this->start_node, $recursive);
        }

        return $tree;
    }

    // ------------------------------------------------------------------------

    /*
     * Get Cache
     *
     * Checks for chached navigation
     *
     * @access protected
     * @param int
     * @param int
     * @param bool
     * @return array or FALSE
     */
    protected function _get_cache($navigation_id, $parent_id = 0, $recursive = true)
    {
        return $this->CI->cache->get(sha1($navigation_id . $parent_id . $recursive), 'navigations');
    }

    // ------------------------------------------------------------------------

    /*
     * Set Cache
     *
     * Writes the navigation cache to file
     *
     * @access protected
     * @param int
     * @param int
     * @param bool
     * @param array
     * @return void
     */
    protected function _set_cache($navigation_id, $parent_id, $recursive, $tree)
    {
        $this->CI->cache->save(sha1($navigation_id . $parent_id . $recursive), 'navigations', $tree);
    }

    // ------------------------------------------------------------------------

    /*
     * Clear Cache
     *
     * Deletes all navigation caches
     *
     * @access public
     * @param int
     * @return void
     */
    public function clear_cache()
    {
        $this->CI->cache->delete_all('navigations');
    }

    // ------------------------------------------------------------------------

    /*
     * Build Tree
     *
     * Queries and constructs the navigation tree from the navigation items db table
     *
     * @access protected
     * @param int
     * @param int
     * @param bool
     * @return array
     */
    protected function _build_tree($navigation_id, $parent_id = 0, $recursive = true)
    {
        $nav_array = array();

        // Using CI ActiveRecord, thinking there might be a slight gain 
        // in performance over Datamapper ORM
        $query = $this->CI->db->select('navigation_items.*, entries.title as entry_title, entries.slug')
            ->from('navigation_items')
            ->join('entries', 'entries.id = navigation_items.entry_id', 'left')
            ->where('navigation_id', $navigation_id)
            ->where('parent_id', $parent_id)
            ->order_by('sort', 'asc')
            ->get();

            foreach ($query->result() as $Item)
            {
                // Generate or parse the nav url and title
                 $Item = $this->_parse_link($Item, TRUE);

                 // Build custom object of item
                 $thisref = new stdClass();
                 $thisref->id                = $Item->id;
                 $thisref->type              = $Item->type;
                 $thisref->entry_id          = $Item->entry_id;
                 $thisref->title             = $Item->title;
                 $thisref->url               = $Item->url;
                 $thisref->tag_id            = $Item->tag_id;
                 $thisref->class             = $Item->class;
                 $thisref->target            = $Item->target;
                 $thisref->parent_id         = $Item->parent_id;
                 $thisref->navigation_id     = $Item->navigation_id;
                 $thisref->sub               = array();
                 $thisref->hide              = $Item->hide;
                 $thisref->sort              = $Item->sort;
                 $thisref->current           = FALSE;
                 $thisref->current_trail     = FALSE;
                 $thisref->subnav_visibility = $Item->subnav_visibility;
                 $thisref->disable_current   = $Item->disable_current;
                 $thisref->disable_current_trail   = $Item->disable_current_trail;
 
                if ($recursive)
                {
                    $thisref->sub = $this->_build_tree($navigation_id, $thisref->id, $recursive);
                }

                $nav_array[] = $thisref;

            }

            $query->free_result();

            return $nav_array;
    }

    // ------------------------------------------------------------------------

    /*
     * Set Current
     *
     * Finds and identifies via URL the current navigaiton item
     *
     * @access protected
     * @param array
     * @param int
     * @return array
     */
    protected function _set_current($nav, $depth = 1)
    {
        // Find current entries in navigation tree
        foreach($nav as &$Item)
        {
            // If URL match set current and current trail TRUE
            if ( ! $Item->disable_current && trim($this->CI->uri->uri_string(), '/') == trim($Item->url, '/') 
                || (current_url() == site_url() && $Item->entry_id == $this->CI->settings->content_module->site_homepage) 
                || ($Item->type == 'dynamic_route' && $this->CI->cms_base_route && strpos(trim($Item->url, '/'), $this->CI->cms_base_route) === 0)) // If current entry does not have a slug default to homepage
            {
                $Item->current = TRUE;
                $Item->current_trail = TRUE;
                $this->current_depth = $depth;
            }

            if (count($Item->sub) > 0)
            {
                $Item->sub = $this->_set_current($Item->sub, $depth + 1);
            }
        }

        unset($Item); // unset last item

        return $nav;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Set Current Trail
     *
     * Finds and identifies ancestors of the current nav item
     *
     * @access protected
     * @param array
     * @param int
     * @return array
     */
    protected function _set_current_trail($nav, $depth = 1)
    {
        // Loop through nav and check if items contain current descendants
        foreach($nav as &$Item)
        {
            if ( ! $Item->disable_current_trail && $this->_has_current_descendant($Item->sub)) 
            {
                $Item->current_trail = TRUE;
            }

            if (count($Item->sub) > 0)
            {
                $Item->sub = $this->_set_current_trail($Item->sub, $depth + 1);
            }
        }

        unset($Item); // unset last item

        return $nav;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Has Current Descendant
     *
     * Detects if item is a ancestor of the current nav item
     *
     * @access protected
     * @param array
     * @param int
     * @return bool
     */
    protected function _has_current_descendant($nav, $depth = 1)
    {
        $has_descendant = FALSE; 

        foreach($nav as $Item)
        {
            // If item is current return true
            if ($Item->current) 
            {
                return TRUE;
            }

            if (count($Item->sub) > 0)
            {
                $has_descendant = $this->_has_current_descendant($Item->sub, $depth + 1);

                if ($has_descendant)
                {
                    return $has_descendant;
                }
            }
        }

        return $has_descendant;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Current Parent Subset
     *
     * Returns a navigation subset starting with the parent of the current nav item at the nth parent depth
     *
     * @access protected
     * @param array
     * @param int
     * @return array
     */
    protected function _current_parent_subset($nav, $depth = 1)
    {
        $subset = array();

        if (count($nav) > 0)
        {
            foreach($nav as $Item)
            {
                if ($Item->current || $Item->current_trail)
                {
                    if ($depth == $this->start_nav_from_parent_depth)
                    {
                        $subset = $nav;
                    }
                    else
                    {
                        $subset = $this->_current_parent_subset($Item->sub, $depth + 1);
                    }

                    break;
                }
            }

        }

        return $subset;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Current Sibling Subset
     *
     * Returns a navigation subset starting with the siblings of the current nav item
     *
     * @access protected
     * @param array
     * @param int
     * @return array
     */
    protected function _current_sibling_subset($nav, $depth = 1)
    {
        $subset = array();

        if (count($nav) > 0)
        {
            foreach($nav as $Item)
            {
                if ($Item->current)
                {
                    $subset = $nav;
                    break;
                }
                else
                {
                    if (count($Item->sub) > 0)
                    {
                        $subset = $this->_current_sibling_subset($Item->sub, $depth + 1);

                        if (count($subset) > 0)
                        {
                            return $subset;
                        }
                    }
                }
            }

        }

        return $subset;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Current Kids Subset
     *
     * Returns a navigation subset starting with the children of the current nav item
     *
     * @access protected
     * @param array
     * @param int
     * @return array
     */
    protected function _current_kids_subset($nav, $depth = 1)
    {
        $subset = array();

        if (count($nav) > 0)
        {
            foreach($nav as $Item)
            {
                if ($Item->current)
                {
                    $subset = $Item->sub;
                    return $subset;
                }

                if (count($Item->sub) > 0)
                {
                    $subset = $this->_current_kids_subset($Item->sub, $depth + 1);

                    if (count($subset) > 0)
                    {
                        return $subset;
                    }
                }
            }

        }

        return $subset;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Parse Link
     *
     * Determins if nav item is a page and sets url and page title
     *
     * @access protected
     * @param object
     * @param bool
     * @return object
     */
    protected function _parse_link($Item, $relative = FALSE)
    {
        // Generate or parse the nav url and title
        if ($Item->type == 'page')
        {
            $Item->title = ($Item->title != '') ? $Item->title : $Item->entry_title;
            $Item->url = ($relative) ? $Item->slug : site_url($Item->slug);
        }
        else if ($Item->type == 'dynamic_route')
        {
            $Item->url = ($relative) ? $Item->url : site_url($Item->url);
        }

        return $Item;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Nav List
     *
     * Initializes navigationa and builds html
     *
     * @access public
     * @param array
     * @return string
     */
    public function list_nav($config = array())
    {
        // Initialize if config passed
        if (count($config) > 0)
        {
            $this->initialize($config);
        }

        if ( ! empty($this->tree))
        {
            if ($this->admin_nav)
            {
                return $this->_list_admin_nav($this->tree);
            }
            else
            {
                return $this->_list_nav($this->tree);
            }
        }
        else
        {
            return '';
        }
    }

    // ------------------------------------------------------------------------
 
    /*
     * Nav List
     *
     * Builds navigation html
     *
     * @access protected
     * @param array
     * @param int
     * @return string
     */
    protected function _list_nav($array, $depth = 1)
    {
        // Return empty string if no nav items found
        if ( empty($array))
        {
            return '';
        }

        // Remove hidden categories from array
        foreach($array as $key => $Item)
        {
            if ($Item->hide)
            {
                unset($array[$key]);
            }
        }

        // Determine the array size
        $output = '';
        $array_count = 1;
        $array_size = count($array);

        if ($this->nested)
        {
            if ($depth == 1)
            {
                $nav = '<ul' . (($this->tag_id) ? ' id="' . $this->tag_id . '"' : '') . (($this->class) ? ' class="' . $this->class . '"' : '') . '>';
            }
            else
            {
                $nav = '<ul>';
            }
        }

        $content = $this->_content;

        // Build unordered list of navigation
        foreach($array as $Item)
        {
            $class = '';

            if ($array_count == 1)
            {
                $class .= ' first';
            }

            if ($array_count == $array_size)
            {
                $class .= ' last';
            }

            if ($Item->current)
            {
                $class .= ' current';
            }

            if ($Item->current_trail)
            {
                $class .= ' current_trail';
            }

            $class = trim($class . ' ' . $Item->class);

            if ($this->nested)
            {
                $nav .= '<li' . ( ! empty($Item->tag_id) ? ' id="' . $Item->tag_id . '"' : '') . ( ! empty($class) ? ' class="' . $class . '"' : '') . '>';
            }

            // Check if content was provided to use
            if ( ! empty($this->_content))
            {
                $item_array = array(
                    'category_id' => $Item->id,
                    'title' => $Item->title,
                    'target' => $Item->target,
                    'id' => $Item->tag_id,
                    'class' => $class,
                    'url' => ($Item->type == 'page' || $Item->type == 'dynamic_route') ? site_url($Item->url) : $Item->url,
                    'path' => ($Item->type == 'page' || $Item->type == 'dynamic_route') ? site_url($Item->url) : $Item->url,
                );

                // Backspace last element if backspace specified
                if ($array_count == $array_size && $this->backspace)
                {
                    $content = substr($content, 0, $this->backspace * -1);
                }

                $nav .= $this->CI->parser->parse_string($content, $item_array, true);
            }
            else
            {
                $nav .= '<a ' . (($Item->target) ? 'target="' . $Item->target . '"': '') . ' href="' . (($Item->type == 'page' || $Item->type == 'dynamic_route') ? site_url($Item->url) : $Item->url) . '">' . $Item->title . '</a>';
            }

            // Check if subnav visibility overridden by parameter else use default settings
            if ( ! empty($Item->sub) && (empty($this->max_depth) || $depth < $this->max_depth))
            {
                if ( ! empty($this->subnav_visibility))
                {
                    if ($this->subnav_visibility == 'show' || ($this->subnav_visibility == 'current_trail' && $Item->current_trail))
                    {
                        $nav .= $this->_list_nav($Item->sub, $depth + 1);
                    }
                }
                else
                {
                    if ($Item->subnav_visibility == 'show' || ($Item->subnav_visibility == 'current_trail' && $Item->current_trail))
                    {
                        $nav .= $this->_list_nav($Item->sub, $depth + 1);
                    }
                }
            }

            if ($this->nested)
            {
                $nav .= '</li>';
            }

            $array_count++;
        }

        if ($this->nested)
        {
            $nav .= '</ul>';
        }

        return $nav;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Admin Nav List
     *
     * Builds navigation html for editing navigation in admin panel
     *
     * @access protected
     * @param array
     * @param int
     * @return string
     */
    protected function _list_admin_nav($array, $depth = 1)
    {
        if ($depth == 1)
        {
            $nav = '<ol class="sortable">';
        }
        else
        {
            $nav = '<ol>';
        }

        foreach($array as $Item)
        {
            $nav .= '<li id="list_' . $Item->id . '">';

            $nav .= '<div><span class="sortable_handle"></span>' . $Item->title . (($Item->hide) ? ' <span class="item_hidden">(Hidden)</span>' : '') . '<span style="float: right;">[ <a href="' . site_url(ADMIN_PATH . "/navigations/items/edit/$this->id/" . $Item->id) . '">Edit</a> ] [ <a class="delete" href="' . site_url(ADMIN_PATH . "/navigations/items/delete/" . $Item->id) . '">Delete</a> ]</span></div>';

            if ( ! empty($Item->sub))
            {
                $nav .= $this->_list_admin_nav($Item->sub, $depth + 1);
            }

            $nav .= '</li>';
        }

        $nav .= '</ol>';

        return $nav;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Breadcrumb
     *
     * Initializes and builds breadcrumbs
     *
     * @access public
     * @param array
     * @return string
     */
    public function breadcrumb($config = array())
    {
        // Reset class data
        $this->clear();

        // Set config
        $this->_set_config($config);

        $Current_crumb = $this->_get_current_crumb();

        if ($Current_crumb !== FALSE)
        {
            $crumbs = $this->_breadcrumb($Current_crumb);

            if ($this->_content == '')
            {
                $data = array(
                    'crumbs' => $crumbs,
                    'tag_id' => $this->tag_id,
                    'class' => $this->class,
                    'breadcrumb_seperator' => $this->breadcrumb_seperator,
                );

                return $this->CI->load->view('breadcrumb', $data, TRUE);
            }
            else
            {
                return $crumbs;
            }
        }

        return '';
    }

    // ------------------------------------------------------------------------
 
    /*
     * Get Current Breadcrumb
     *
     * Queries database for the current navigation item
     *
     * @access protected
     * @return object or FALSE
     */
    protected function _get_current_crumb()
    {
        $this->CI->db->select('navigation_items.*')
            ->from('navigation_items')
            ->where('navigation_items.navigation_id', $this->id);

        if ($this->CI->cms_base_route != '')
        {
            $this->CI->db->where('navigation_items.type', 'dynamic_route')
                ->where('navigation_items.url', trim($this->CI->cms_base_route, '/'));
        }
        else
        {
            $this->CI->db->select('entries.title as entry_title, entries.slug')
            ->join('entries', 'entries.id = navigation_items.entry_id', 'left')
            ->where('navigation_items.entry_id IS NOT NULL')
            ->where('navigation_items.disable_current', 0)
            ->where('entries.slug', trim($this->CI->uri->uri_string(), '/'));
        }

        $query = $this->CI->db->limit(1)->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->row();

            $query->free_result();

            return $this->_parse_link($result);
        }
        else
        {
            $query->free_result();

            return FALSE;
        }
    }

    // ------------------------------------------------------------------------
 
    /*
     * Breadcrumb
     *
     * Queries and builds html for breadcrumb
     *
     * @access protected
     * @return string
     */
    protected function _breadcrumb($Item, $depth = 1)
    {
        $crumbs = array();

        // Build current on first depth
        if ($depth == 1)
        {
            $Item->class = trim($Item->class . ' current last');
            $Item->id = trim($Item->tag_id);
            $Item->url = current_url();
            $crumbs[] = object_to_array($Item);
        }
        else
        {
            if ($Item->parent_id == 0)
            {
                $Item->class .= ' first';
            }

            // List Item Attributes
            $Item->class = trim($Item->class);
            $Item->id = trim($Item->tag_id);
            $Item->url = (($Item->type == 'url') ? $this->CI->parser->parse_string($Item->url, array(), TRUE) : $Item->url);

            $crumbs[] = object_to_array($Item);
        } 

        // Check if item has a parent to process
        if ($Item->parent_id > 0)
        {
            $query = $this->CI->db->select('navigation_items.*, entries.title as entry_title, entries.slug')
                ->from('navigation_items')
                ->join('entries', 'entries.id = navigation_items.entry_id', 'left')
                ->where('navigation_items.id', $Item->parent_id)
                ->limit(1)
                ->get();

            if ($query->num_rows() > 0)
            {
                $result = $query->row();
                $result = $this->_parse_link($result);
                $crumbs = array_merge($this->_breadcrumb($result, $depth + 1), $crumbs);
            }

            $query->free_result();
        }

        return $crumbs;
    }
}
