<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories_library
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
        $this->path = null;
        $this->admin_categories = FALSE;
        $this->admin_entries_categories = FALSE;
        $this->nested = TRUE;
        $this->populate = array();
        $this->max_depth = 0;
        $this->backspace = 0;
        $this->_content = '';
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
     * Sets config and builds out category tree
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
        $this->tree = $this->_get_category_group();

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
     * Get Category Group
     *
     * Checks and returns a cached category group, if there is no cached version of the category group it will create one
     *
     * @access protected
     * @param bool
     * @return array
     */
    protected function _get_category_group($cache = true)
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
     * Checks for chached category group
     *
     * @access protected
     * @param int
     * @param int
     * @param bool
     * @return array or FALSE
     */
    protected function _get_cache($category_group_id, $parent_id = 0, $recursive = true)
    {
        return $this->CI->cache->get(sha1($category_group_id . $parent_id . $recursive), 'categories');
    }

    // ------------------------------------------------------------------------

    /*
     * Set Cache
     *
     * Writes the category group cache to file
     *
     * @access protected
     * @param int
     * @param int
     * @param bool
     * @param array
     * @return void
     */
    protected function _set_cache($category_group_id, $parent_id, $recursive, $tree)
    {
        $this->CI->cache->save(sha1($category_group_id . $parent_id . $recursive), 'categories', $tree);
    }

    // ------------------------------------------------------------------------

    /*
     * Clear Cache
     *
     * Deletes all category caches
     *
     * @access public
     * @param int
     * @return void
     */
    public function clear_cache()
    {
        $this->CI->cache->delete_all('categories');
    }

    // ------------------------------------------------------------------------

    /*
     * Build Tree
     *
     * Queries and constructs the category tree from the categories db table
     *
     * @access protected
     * @param int
     * @param int
     * @param bool
     * @return array
     */
    protected function _build_tree($category_group_id, $parent_id = 0, $recursive = true)
    {
        $categories_array = array();

        // Using CI ActiveRecord, thinking there might be a slight gain 
        // in performance over Datamapper ORM
        $Query = $this->CI->db->select('categories.*')
            ->from('categories')
            ->where('category_group_id', $category_group_id)
            ->where('parent_id', $parent_id)
            ->order_by('sort', 'asc')
            ->get();

            foreach ($Query->result() as $Category)
            {
                 // Build custom object of item
                 $thisref = new stdClass();
                 $thisref->id                = $Category->id;
                 $thisref->title             = $Category->title;
                 $thisref->url_title         = $Category->url_title;
                 $thisref->tag_id            = $Category->tag_id;
                 $thisref->class             = $Category->class;
                 $thisref->target            = $Category->target;
                 $thisref->parent_id         = $Category->parent_id;
                 $thisref->category_group_id = $Category->category_group_id;
                 $thisref->sub               = array();
                 $thisref->hide              = $Category->hide;
                 $thisref->sort              = $Category->sort;
                 $thisref->current           = FALSE;
                 $thisref->current_trail     = FALSE;
                 $thisref->subcategories_visibility = $Category->subcategories_visibility;
 
                if ($recursive)
                {
                    $thisref->sub = $this->_build_tree($category_group_id, $thisref->id, $recursive);
                }

                $categories_array[] = $thisref;

            }

            $Query->free_result();

            return $categories_array;
    }

    // ------------------------------------------------------------------------

    /*
     * Set Current
     *
     * Finds and identifies via URL the current category
     *
     * @access protected
     * @param array
     * @param int
     * @return array
     */
    protected function _set_current($tree, $depth = 1, $url_title = '')
    {
        if ($url_title == '')
        {
            $parameters = $this->CI->cms_parameters;
            $category_index = array_search('category', $parameters);

            /// Check that category and a url title was found
            if ($category_index === FALSE || ! isset($parameters[$category_index + 1]))
            {
                return $tree;
            }

            $url_title = $parameters[$category_index + 1];
        }

        // Find current category in categories tree
        foreach($tree as &$Category)
        {
            // If URL match set current and current trail TRUE
            if ($Category->url_title == $url_title)
            {
                $Category->current = TRUE;
                $Category->current_trail = TRUE;
                $this->current_depth = $depth;
            }

            if (count($Category->sub) > 0)
            {
                $Category->sub = $this->_set_current($Category->sub, $depth + 1, $url_title);
            }
        }

        unset($Category); // unset last item

        return $tree;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Set Current Trail
     *
     * Finds and identifies ancestors of the current category
     *
     * @access protected
     * @param array
     * @param int
     * @return array
     */
    protected function _set_current_trail($tree, $depth = 1)
    {
        // Loop through category group and check if categories contain current descendants
        foreach($tree as &$Category)
        {
            if ($this->_has_current_descendant($Category->sub)) 
            {
                $Category->current_trail = TRUE;
            }

            if (count($Category->sub) > 0)
            {
                $Category->sub = $this->_set_current_trail($Category->sub, $depth + 1);
            }
        }

        unset($Category); // unset last item

        return $tree;
    }

    // ------------------------------------------------------------------------
 
    /*
     * Has Current Descendant
     *
     * Detects if category is a ancestor of the current category
     *
     * @access protected
     * @param array
     * @param int
     * @return bool
     */
    protected function _has_current_descendant($tree, $depth = 1)
    {
        $has_descendant = FALSE; 

        foreach($tree as $Category)
        {
            // If item is current return true
            if ($Category->current) 
            {
                return TRUE;
            }

            if (count($Category->sub) > 0)
            {
                $has_descendant = $this->_has_current_descendant($Category->sub, $depth + 1);

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
     * Returns a category group subset starting with the parent of the current category at the nth parent depth
     *
     * @access protected
     * @param array
     * @param int
     * @return array
     */
    protected function _current_parent_subset($tree, $depth = 1)
    {
        $subset = array();

        if (count($tree) > 0)
        {
            foreach($tree as $Category)
            {
                if ($Category->current || $Category->current_trail)
                {
                    if ($depth == $this->start_nav_from_parent_depth)
                    {
                        $subset = $tree;
                    }
                    else
                    {
                        $subset = $this->_current_parent_subset($Category->sub, $depth + 1);
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
     * Returns a category group subset starting with the siblings of the current category
     *
     * @access protected
     * @param array
     * @param int
     * @return array
     */
    protected function _current_sibling_subset($tree, $depth = 1)
    {
        $subset = array();

        if (count($tree) > 0)
        {
            foreach($tree as $Category)
            {
                if ($Category->current)
                {
                    $subset = $tree;
                    break;
                }
                else
                {
                    if (count($Category->sub) > 0)
                    {
                        $subset = $this->_current_sibling_subset($Category->sub, $depth + 1);

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
     * Returns a category group subset starting with the children of the current category
     *
     * @access protected
     * @param array
     * @param int
     * @return array
     */
    protected function _current_kids_subset($tree, $depth = 1)
    {
        $subset = array();

        if (count($tree) > 0)
        {
            foreach($tree as $Category)
            {
                if ($Category->current)
                {
                    $subset = $Category->sub;
                    return $subset;
                }

                if (count($Category->sub) > 0)
                {
                    $subset = $this->_current_kids_subset($Category->sub, $depth + 1);

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
     * List Categories
     *
     * Initializes category groups and builds html
     *
     * @access public
     * @param array
     * @return string
     */
    public function list_categories($config = array())
    {
        // Initialize if config passed
        if (count($config) > 0)
        {
            $this->initialize($config);
        }

        if (empty($this->tree))
        {
            return '';
        }
        else
        {
            if ($this->admin_categories)
            {
                return $this->_list_admin_categories($this->tree);
            }
            else if ($this->admin_entries_categories)
            {
                return $this->_list_admin_entries_categories($this->tree);
            }
            else
            {
                return $this->_list_categories($this->tree);
            }
        }
    }

    // ------------------------------------------------------------------------
 
    /*
     * List Categories
     *
     * Builds category tree html
     *
     * @access protected
     * @param array
     * @param int
     * @return string
     */
    protected function _list_categories($tree, $depth = 1)
    {
        // Return empty string if no categories found
        if (empty($tree))
        {
            return '';
        }

        // Remove hidden categories from array
        foreach($tree as $key => $Category)
        {
            if ($Category->hide)
            {
                unset($tree[$key]);
            }
        }

        // Determine the array size
        $output = '';
        $array_count = 1;
        $array_size = count($tree);

        if ($this->nested)
        {
            if ($depth == 1)
            {
                $output = '<ul' . (($this->tag_id) ? ' id="' . $this->tag_id . '"' : '') . (($this->class) ? ' class="' . $this->class . '"' : '') . '>';
            }
            else
            {
                $output = '<ul>';
            }
        }

        // Determine if a base path was passed to the path variable tag
        if (preg_match('/\{\{\s*path\s*=\s*[\'"](.*?)[\'"]\s*\}\}/', $this->_content, $match))
        {
            if (isset($match[1]))
            {
                $base_path = trim($match[1], '/');
            }
        }
        else
        {
            $base_path = trim($this->path, '/');
        }

        $content = $this->_content;

        // Build data array
        foreach($tree as $Category)
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

            if ($Category->current)
            {
                $class .= ' current';
            }

            if ($Category->current_trail)
            {
                $class .= ' current_trail';
            }

            $class = trim($class . ' ' . $Category->class);

            if ($this->nested)
            {
                $output .= '<li' . ( ! empty($Category->tag_id) ? ' id="' . $Category->tag_id . '"' : '') . ( ! empty($class) ? ' class="' . $class . '"' : '') . '>';
            }

            // Check if content was provided to use
            if ( ! empty($this->_content))
            {
                $category_array = array(
                    'category_id' => $Category->id,
                    'title' => $Category->title,
                    'target' => $Category->target,
                    'id' => $Category->tag_id,
                    'class' => $class,
                    'url_title' => $Category->url_title,
                    'path' =>  site_url($base_path . '/category/' . $Category->url_title),
                );

                // Backspace last element if backspace specified
                if ($array_count == $array_size && $this->backspace)
                {
                    $content = substr($content, 0, $this->backspace * -1);
                }

                $output .= $this->CI->parser->parse_string($content, $category_array, true);
            }
            else
            {
                $output .= '<a ' . (($Category->target) ? 'target="' . $Category->target . '"': '') . ' href="' . site_url($base_path . '/category/' . $Category->url_title) . '">' . $Category->title . '</a>';
            }

            // Check if subcategories visibility overridden by parameter else use default settings
            if ( ! empty($Category->sub) && (empty($this->max_depth) || $depth < $this->max_depth))
            {
                if ( ! empty($this->subcategories_visibility))
                {
                    if ($this->subcategories_visibility == 'show' || ($this->subcategories_visibility == 'current_trail' && $Category->current_trail))
                    {
                        $output .= $this->_list_categories($Category->sub, $depth + 1);
                    }
                }
                else
                {
                    if ($Category->subcategories_visibility == 'show' || ($Category->subcategories_visibility == 'current_trail' && $Category->current_trail))
                    {
                        $output .= $this->_list_categories($Category->sub, $depth + 1);
                    }
                }
            }

            if ($this->nested)
            {
                $output .= '</li>';
            }

            $array_count++;
        }

        if ($this->nested)
        {
            $output .= '</ul>';
        }

        return $output;
    }

    // ------------------------------------------------------------------------
 
    /*
     * List Admin Categories
     *
     * Builds tree html for editing categories in admin panel
     *
     * @access protected
     * @param array
     * @param int
     * @return string
     */
    protected function _list_admin_categories($tree, $depth = 1)
    {
        if ($depth == 1)
        {
            $output = '<ol class="sortable">';
        }
        else
        {
            $output = '<ol>';
        }

        foreach($tree as $Category)
        {
            $output .= '<li id="list_' . $Category->id . '">';

            $output .= '<div><span class="sortable_handle"></span>' . $Category->title . (($Category->hide) ? ' <span class="item_hidden">(Hidden)</span>' : '') . '<span style="float: right;">[ <a href="' . site_url(ADMIN_PATH . "/content/categories/edit/$this->id/" . $Category->id) . '">Edit</a> ] [ <a class="delete" href="' . site_url(ADMIN_PATH . "/content/categories/delete/" . $Category->id) . '">Delete</a> ]</span></div>';

            if ( ! empty($Category->sub))
            {
                $output .= $this->_list_admin_categories($Category->sub, $depth + 1);
            }

            $output .= '</li>';
        }

        $output .= '</ol>';

        return $output;
    }

    // ------------------------------------------------------------------------
 
    /*
     * List Admin Entries Categories
     *
     * Builds html tree for assigning categories to entries in the admin panel
     *
     * @access protected
     * @param array
     * @param int
     * @return string
     */
    protected function _list_admin_entries_categories($tree, $depth = 1)
    {
        $output = '<ul>';

        foreach($tree as $Category)
        {
            $output .= '<li>';

            $output .= '<label><input type="checkbox" name="categories[]" value="' . $Category->id . '" ' . set_checkbox('categories[]', $Category->id, in_array($Category->id, $this->populate))  . '>' . $Category->title . '</label>';

            if ( ! empty($Category->sub))
            {
                $output .= $this->_list_admin_entries_categories($Category->sub, $depth + 1);
            }

            $output .= '</li>';
        }

        $output .= '</ul>';

        return $output;
    }
}
