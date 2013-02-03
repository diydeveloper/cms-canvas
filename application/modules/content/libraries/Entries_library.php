<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entries_library
{
    private $CI;
    private $pagenum_segment = NULL;
    private $db = NULL;

    public $entries = array();
    public $content_fields = array();
    public $entry_id = NULL;
    public $content_type = NULL;
    public $category = NULL;
    public $order_by = NULL;
    public $sort = 'asc';
    public $segment = NULL;
    public $limit = NULL;
    public $paginate = FALSE;
    public $backspace = 0;
    public $_content = '';

    /*
     * Construct
     *
     * Defines CI instance to class
     *
     * @return void
     */
    function __construct()
    {
        $this->CI =& get_instance();    
        $this->CI->load->add_package_path(APPPATH . 'modules/content/content_fields');

        // Create a new instance of the activerecord class
        // And clear benchmarks for profiling
        $this->db =& $this->CI->db;

        $this->CI->parser->set_callback('categories', array($this, 'categories_callback'));
        $this->CI->parser->set_callback('author', array($this, 'author_callback'));
        $this->CI->parser->set_callback('created_date', array($this, 'date_format_callback'));
        $this->CI->parser->set_callback('modified_date', array($this, 'date_format_callback'));
    }

    // ------------------------------------------------------------------------

    /*
     * Initialize
     *
     * Defines CI instance to class
     *
     * @param array
     * @return void
     */
    function initialize($config = array())
    {
        foreach ($config as $key => $value)
        {
            $this->$key = $value;
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Entries
     *
     * Executes functions to build the entries query
     *
     * @return array or string
     */
    function entries()
    {
        // If an entry_id is specified we will treat it as a page partial and not even bother to query.
        // This will cache and return the content build
        if ( ! is_null($this->entry_id))
        {
            $this->CI->load->library('cache');
            $Entry = $this->CI->cache->model('entries_cache_model', 'cacheable_get_by', array('id' => $this->entry_id), 'entries');
            $Entry->dynamic_route = $Entry->content_types->dynamic_route;
            $Entry->layout = $Entry->content_types->layout;
            $Entry->short_name = $Entry->content_types->short_name;
            $this->content_fields = $Entry->content_fields;

            // Format Entry object for use with build_entry_data()
            foreach(get_object_vars($Entry->entry_data) as $name => $value)
            {
                $Entry->$name = $value;
            }

            $this->build_entry_data($Entry);

            return $this->entries;
        }

        // Query to get content fields
        $this->_get_content_fields();

        // Build the field select statement for the entries_data table
        $entries_data_select = '';
        foreach($this->content_fields as $Field)
        {
            $entries_data_select .= ', field_id_' . $Field->id;
        }

        // Uses standard CI Active Record to minimize overhead
        $this->db->select('entries.*' . $entries_data_select .  ', content_types.dynamic_route, content_types.layout, content_types.short_name')
            ->from('entries')
            ->join('entries_data', 'entries.id = entries_data.entry_id')
            ->join('content_types', 'entries.content_type_id = content_types.id');

        // Show published and drafts for administrators
        // everyone else only show published
        if ($this->CI->secure->group_types(array(ADMINISTRATOR))->is_auth())
        {
            $this->db->where_in('status', array('published', 'draft'));
        }
        else
        {
            $this->db->where('status', 'published');
        }

        $this->_content_type();
        $this->_category();
        $this->_segment();
        $this->_archive_filter();
        $this->_category_filter();

        // Clone query before setting a limit for use in pagination result count
        // and reset benchmarks for profiling
        $Query_clone = clone $this->db;
        $Query_clone->benchmark = 0;
        $Query_clone->query_count = 0;
        $Query_clone->queries = array();
        $Query_clone->query_times = array();

        $this->_order_by();
        $this->_limit();

        $Query = $this->db->get();

        // Show 404 page if no results found and a segment was specified
        if ( ! $Query->num_rows() && ! empty($this->segment))
        {
            return show_404();
        }
        elseif ( ! $Query->num_rows)
        {
            return $this->_no_results();
        }
        else
        {
            // Define tags for entries
            $this->build_entry_data($Query->result());

            $this->_before_iteration();
            $this->_after_iteration();

            $this->_paginate($Query_clone);
        }

        return $this->entries;
    }

    // ------------------------------------------------------------------------

    /*
     * Build Entry Data
     *
     * Fetches entries and constructs an array of data or a string if not content formmatting specified 
     *
     * @return void
     */
    public function build_entry_data($query_results)
    {
        // Make query_results an array if not already
        if ( ! is_array($query_results))
        {
            $query_results = array($query_results);
        }

        $total_results = count($query_results);
        $count = 0;

        foreach($query_results as $Entry)
        {
            $count++;

            $content['entry_id'] = $Entry->id;

            if (is_inline_editable($Entry->content_type_id))
            {
                $content['title_editable'] = '<div id="cc_field_' . $Entry->id . '_title" class="cc_admin_editable cc_text_editable" contenteditable="true">{{ noparse }}' . $Entry->title . '{{ /noparse }}</div>';
            }
            else
            {
                $content['title_editable'] = $Entry->title;
            }

            $content['title'] = $Entry->title;
            $content['created_date'] = $Entry->created_date;
            $content['modified_date'] = $Entry->modified_date;
            $content['url_title'] = $Entry->url_title;
            $content['slug'] = $Entry->slug;
            $content['dynamic_route'] = $Entry->dynamic_route;
            $content['content_type'] = $Entry->short_name;
            $content['count'] = $count;
            $content['total_results'] = $total_results;
            $content['author_id'] = $Entry->author_id;
            $content['meta_title'] = $Entry->meta_title;
            $content['meta_description'] = $Entry->meta_description;
            $content['meta_keywords'] = $Entry->meta_keywords;

            foreach ($this->content_fields as $Field)
            {
                // Only define tags that belong to this content type
                if ($Field->content_type_id == $Entry->content_type_id)
                {
                    $field_type = Field_type::factory($Field->model_name, $Field, $Entry);
                    $content[$Field->short_tag] = $field_type->output();

                    if (method_exists($field_type, 'parser_callback'))
                    {
                        $content['_callbacks'][$Field->short_tag] = array($field_type, 'parser_callback');
                    }
                }
            }

            // Create a dynamic url tag for entries that have a content type with a dynamic route defined
            // If the content type does not have a dynamic route, it ouputs the entries id
            if ($Entry->dynamic_route != '')
            {
                $content['dynamic_url'] = site_url($Entry->dynamic_route . '/' . (($Entry->url_title != '') ? $Entry->url_title : $Entry->id));
            }
            else
            {
                $content['dynamic_url'] = $Entry->id;
            }

            // Use content type's layout if no content defined
            if ($this->_content == '')
            {
                $this->_content = $Entry->layout;
                $content['_content'] = $this->_content;
            }

            if ($count == $total_results && $this->backspace)
            {
                $stripped_content = preg_replace('/\n?\s*?\{\{\s*(paginate|before_iteration|after_iteration|no_results)\s*\}\}(.*?)\{\{\s*\/\1\s*\}\}/ms', '', $this->_content);
                $stripped_content = substr($stripped_content, 0, $this->backspace * -1);
                $content['_content'] = $stripped_content;
            }
            
            $this->entries[] = $content;
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Content Type
     *
     * Queries for specific content types
     *
     * @return void
     */
    private function _content_type(&$DB_object = null)
    {
        if ( ! is_null($this->content_type))
        {
            // If no db object was passed. Default to the class db object
            if ( ! is_object($DB_object))
            {
                $DB_object =& $this->db;
            }
            
            // Check if not keyword was used to do a not equal condition
            $not_position = stripos($this->content_type, 'not ');

            if ($not_position === 0)
            {
                $content_type = substr($this->content_type, 4) ;
                $DB_object->where_not_in('short_name', explode('|', $content_type));
            }
            else
            {
                $DB_object->where_in('short_name', explode('|', $this->content_type));
            }
        } 
    }

    // ------------------------------------------------------------------------

    /*
     * Order By
     *
     * Adds the order by clause to the entries query
     *
     * @return void
     */
    private function _order_by()
    {
        // Query Order
        if ( ! is_null($this->order_by))
        {
            if ($this->order_by == 'random')
            {
                $this->db->order_by('rand()');
            }
            else
            {
                $order_by = explode('|', $this->order_by);
                $sort = explode('|', $this->sort);
                $concat_count = 1;

                foreach ($order_by as $index => $short_tag)
                {
                    $concat_fields = array();
                    $field = $short_tag;

                    foreach ($this->content_fields as $Content_field)
                    {
                        if ($Content_field->short_tag == $short_tag)
                        {
                            if ( ! empty($concat_fields))
                            {
                                // We have already found a field by this short tag so we will
                                // sort by concatenated custom field
                                $field = 'concatenated_field_' . $concat_count;
                            }
                            else
                            {
                                $field = 'field_id_' . $Content_field->id;
                            }

                            $concat_fields[] = 'field_id_' . $Content_field->id;
                        }
                    }

                    // If we have more than one short tag  that matches the
                    // order_by, concatenate the fields and sort on new field
                    if (count($concat_fields) > 1)
                    {
                        $this->db->select("CONCAT_WS('', `" . implode("`, `", $concat_fields) . "`) as `concatenated_field_" . $concat_count . "`", FALSE);
                        $concat_count++;
                    }

                    $this->db->order_by($field, (isset($sort[$index])) ? $sort[$index] : 'asc');
                }
            }
        } 
    }

    // ------------------------------------------------------------------------

    /*
     * Segment
     *
     * Queries entries based on a defined segment in the URL
     *
     * @return void
     */
    private function _segment()
    {
        //  URL Title
        if ( ! empty($this->segment))
        {
            $url_param = $this->CI->uri->segment($this->segment);

            if ($url_param == '')
            {
                return show_404();
            }

            if (is_numeric($url_param))
            {
                $this->db->where('`entries`.id', $url_param);
            }
            else
            {
                $this->db->where('`entries`.url_title', $url_param);
            }
        } 
    }

    // ------------------------------------------------------------------------

    /*
     * Limit
     *
     * Adds a limit to the entries query if specified
     *
     * @return void
     */
    private function _limit()
    {
        // Query limit
        if ( ! is_null($this->limit))
        {
            $pagenum_segment = $this->_locate_pagenum_segment();

            if ($pagenum_segment)
            {
                $offset = (ltrim($this->CI->uri->segment($pagenum_segment), 'p') - 1) * $this->limit;
            }
            else
            {
                $offset = NULL;
            }

            $this->db->limit($this->limit, $offset);
        } 
    }

    // ------------------------------------------------------------------------

    /*
     * Paginate
     *
     * If enabled, parses content for paginate tags and builds pagination links
     *
     * @return void
     */
    private function _paginate($Query_clone)
    {
        if ($this->paginate)
        {
            $pagination_content = '';

            // Check if paginate tags found and set content
            if (preg_match('/\{\{\s*paginate\s*\}\}(.*?)\{\{\s*\/paginate\s*\}\}/ms', $this->_content, $paginate_matches))
            {
                $pagination_content = $paginate_matches[1];
            }
            else
            {
                // No paginate block found. 
                // No further pagination processing will be needed.
                return;
            }

            $total_results = $Query_clone->count_all_results();

            // Tell the singleton db object about our queries for profiling
            $this->CI->db->benchmark = $this->CI->db->benchmark + $Query_clone->benchmark;
            $this->CI->db->query_count = $this->CI->db->query_count + $Query_clone->query_count;
            $this->CI->db->queries = array_merge($this->CI->db->queries, $Query_clone->queries);
            $this->CI->db->query_times = array_merge($this->CI->db->query_times, $Query_clone->query_times);

            $pagenum_segment = $this->_locate_pagenum_segment();

            // Determine the pagination base URL which includes everthing up to to pagenum segment
            if ($pagenum_segment)
            {
                $base_url = array_slice($this->CI->uri->segment_array(), 0, $pagenum_segment - 1);
            }
            else
            {
                $base_url = $this->CI->uri->uri_string();
            }

            // Load and initialize pagination
            $Pagination =& $this->CI->load->library('pagination');
            $config['base_url'] = site_url($base_url);
            $config['total_rows'] = $total_results;
            $config['per_page'] = $this->limit; 
            $config['use_page_numbers'] = TRUE;
            $config['prefix'] = 'p';
            $config['uri_segment'] = $pagenum_segment;

            $Pagination->initialize($config);

            // Define pagination tags 
            $content = array();
            $content['pagination_links'] = $Pagination->create_links();
            $content['total_pages'] = $total_pages = ($this->limit != NULL) ? ceil($total_results / $this->limit) : 0;
            $content['current_page'] = $current_page = $Pagination->cur_page;

            // Check if next page conditional found and set content
            if (preg_match('/\{\{\s*if\s*next_page\s*\}\}(.*?)\{\{\s*endif\s*\}\}/ms', $pagination_content, $next_page_cond_matches))
            {
                if ($current_page < $total_pages)
                {
                    $parse_data['pagination_url'] = $Pagination->base_url . $Pagination->prefix . ($current_page + 1) . $Pagination->suffix;
                    $next_page_content = $this->CI->parser->parse_string($next_page_cond_matches[1], $parse_data, TRUE);
                    $pagination_content = preg_replace('/\{\{\s*if\s*next_page\s*\}\}(.*?)\{\{\s*endif\s*\}\}/ms', $next_page_content, $pagination_content);
                }
                else
                {
                    $pagination_content = preg_replace('/\{\{\s*if\s*next_page\s*\}\}(.*?)\{\{\s*endif\s*\}\}/ms', '', $pagination_content);
                }
            }

            // Check if previous page conditional found and set content
            if (preg_match('/\{\{\s*if\s*previous_page\s*\}\}(.*?)\{\{\s*endif\s*\}\}/ms', $pagination_content, $prev_page_cond_matches))
            {
                if ($current_page != 1)
                {
                    $parse_data['pagination_url'] = $Pagination->base_url . $Pagination->prefix . ($current_page - 1) . $Pagination->suffix;
                    $prev_page_content = $this->CI->parser->parse_string($prev_page_cond_matches[1], $parse_data, TRUE);
                    $pagination_content = preg_replace('/\{\{\s*if\s*previous_page\s*\}\}(.*?)\{\{\s*endif\s*\}\}/ms', $prev_page_content, $pagination_content);
                }
                else
                {
                    $pagination_content = preg_replace('/\{\{\s*if\s*previous_page\s*\}\}(.*?)\{\{\s*endif\s*\}\}/ms', '', $pagination_content);
                }
            }

            $content['_content'] = $pagination_content;

            // Adds pagination to top, bottom, or both of the passed content
            if ($this->paginate == 'both')
            {
                array_unshift($this->entries, $content);
                $this->entries[] = $content;
            }
            else if ($this->paginate == 'top')
            {
                array_unshift($this->entries, $content);
            }
            else
            {
                $this->entries[] = $content;
            }

        } 
    }

    // ------------------------------------------------------------------------

    /*
     * Locate Page Number Segment
     *
     * Attepts to find the segment number in the URI that defines the current page number
     *
     * @return int
     */
    private function _locate_pagenum_segment()
    {
        if ( ! is_null($this->pagenum_segment))
        {
            return $this->pagenum_segment;
        }

        $i = 1;
        foreach ($this->CI->cms_parameters as $param)
        {
            // Find a paramater starting with p followed by an integer
            if (preg_match('/^p\d+$/', $param))
            {
                // Total URI segments minus the number of parameters plus array position of matched parameter
                // This will return the segment number based on the full URI
                return $this->pagenum_segment = ($this->CI->uri->total_segments() - count($this->CI->cms_parameters)) + $i;
            }

            $i++;
        }

        return $this->pagenum_segment = FALSE;
    }

    // ------------------------------------------------------------------------

    /*
     * Get Content Fields
     *
     *  Queries all content fields
     *
     * @return array
     */
    private function _get_content_fields()
    {
        if ( ! empty($this->content_fields))
        {
            return $this->content_fields;
        }

        $all_content_fields = $this->CI->cache->model('content_types_cache_model', 'cacheable_content_fields', array(), 'content_types');
        $selected_content_fields = array();

        // If content_type defined only get those fields
        if ( ! is_null($this->content_type))
        {
            // Check if not keyword was used to do a not equal condition
            $not_position = stripos($this->content_type, 'not ');

            if ($not_position === 0)
            {
                $selected_content_fields = $all_content_fields;

                foreach (explode('|', substr($this->content_type, 4)) as $content_type)
                {
                    unset($selected_content_fields[$content_type]);
                }
            }
            else
            {
                foreach (explode('|', $this->content_type) as $content_type)
                {
                    if (isset($all_content_fields[$content_type]))
                    {
                        $selected_content_fields[$content_type] = $all_content_fields[$content_type];
                    }
                    else
                    {
                        $selected_content_fields[$content_type] = array();
                    }
                }
            }
        } 
        else
        {
            $selected_content_fields = $all_content_fields;
        }

        foreach ($selected_content_fields as $content_type => $content_fields)
        {
            $this->content_fields = array_merge($this->content_fields, $content_fields);
        }

        return $this->content_fields;
    }

    // ------------------------------------------------------------------------

    /*
     * Category
     *
     * Filters entries based on provided category parameter
     *
     * @return void
     */
    private function _category()
    {
        // If content_type defined only get those fields
        if (is_null($this->category))
        {
            return FALSE;
        }

        // Join the categories table
        $this->db->distinct();
        $this->db->join('categories_entries', 'entries.id = categories_entries.entry_id');

        // Check if not keyword was used to do a not equal condition
        $not_position = stripos($this->category, 'not ');

        if ($not_position === 0)
        {
            $category = substr($this->category, 4);
            $category_array = explode('|', str_replace('&', '|', $category));

            $this->db->where_not_in('category_id', $category_array);
        }
        else
        {
            $category_array = explode('|', str_replace('&', '|', $this->category));
            $this->db->where_in('category_id', $category_array);
        }

        if (strpos($this->category, '&') !== FALSE)
        {
            $this->db->select('COUNT(entries.id) as entry_total');
            $this->db->having('entry_total', count($category_array));
        }
    }

    // ------------------------------------------------------------------------

    /*
     * No Results
     *
     * Parse content to check for no_results tag if no entries were found
     *
     * @return string
     */
    private function _no_results()
    {
        // Find and match paginate tags in the content
        $paginate_match = preg_match('/\{\{\s*no_results\s*\}\}(.*?)\{\{\s*\/no_results\s*\}\}/ms', $this->_content, $matches);

        // Check if no_results tags found and set content
        if ($paginate_match && isset($matches[1]))
        {
            return $matches[1];
        }

        return '';
    }


    // ------------------------------------------------------------------------

    /*
     * Before Iteration
     *
     * Parse content to check for before_iteration tag
     *
     * @return string
     */
    private function _before_iteration()
    {
        // Find and match paginate tags in the content
        $paginate_match = preg_match('/\{\{\s*before_iteration\s*\}\}(.*?)\{\{\s*\/before_iteration\s*\}\}/ms', $this->_content, $matches);

        // Check if no_results tags found and set content
        if ($paginate_match && isset($matches[1]))
        {
            $content['_content'] = $matches[1];
            array_unshift($this->entries, $content);
        }
    }

    // ------------------------------------------------------------------------

    /*
     * After Iteration
     *
     * Parse content to check for after_iteration tag
     *
     * @return string
     */
    private function _after_iteration()
    {
        // Find and match paginate tags in the content
        $paginate_match = preg_match('/\{\{\s*after_iteration\s*\}\}(.*?)\{\{\s*\/after_iteration\s*\}\}/ms', $this->_content, $matches);

        // Check if no_results tags found and set content
        if ($paginate_match && isset($matches[1]))
        {
            $content['_content'] = $matches[1];
            $this->entries[] = $content;
        }
    }

    // ------------------------------------------------------------------------

    /*
     * Author Callback
     *
     * Checks if author tag exists in content
     * If author tag found users table will be joined to the entries query
     *
     * @return string or array
     */
    public function author_callback($trigger, $parameters, $content, $data)
    {
        if ( ! isset($data['author_id']))
        {
            return '';
        }

        $this->db->from('users');
        $this->db->where('`id`', $data['author_id']);
        $Query = $this->db->get();

        if ($Query->num_rows > 0)
        {
            $User = $Query->row(0);

            if ( ! $content)
            {
                return $User->first_name . ' ' . $User->last_name;
            }
            else
            {
                return object_to_array($User);
            }
        }

        return '';
    }

    // ------------------------------------------------------------------------

    /*
     * Archive Filter
     *
     * Queries entries based on a month and year specified in the URL
     *
     * @return void
     */
    private function _archive_filter()
    {
        if ( ! empty($this->CI->cms_parameters))
        {
            $parameters = $this->CI->cms_parameters;
            $archive_index = array_search('archive', $parameters);

            /// Check that archive and a year was found and validate the year
            if ($archive_index === FALSE || ! isset($parameters[$archive_index + 1]) ||  ! preg_match('/^\d{4}$/', $parameters[$archive_index + 1]))
            {
                return;
            }

            // A valid year was found so filter entries
            $year = $parameters[$archive_index + 1];
            $this->db->where('YEAR(`entries`.`created_date`)', $year);

            /// Check if a month was specified and validate the month
            if ( ! isset($parameters[$archive_index + 2]) || ! preg_match('/^0[1-9]|1[012]$/', $parameters[$archive_index + 2]))
            {
                return;
            }

            // A valid month was found so filter entries
            $month = $parameters[$archive_index + 2];
            $this->db->where('MONTH(`entries`.`created_date`)', $month);
        } 
    }

    // ------------------------------------------------------------------------

    /*
     * Categories
     *
     * Queries for categories assigned to the specified entry id
     *
     * @return int
     * @return array
     */
    public function categories_callback($trigger, $parameters, $content, $data)
    {
        $this->db->select('categories.*');
        $this->db->from('categories');
        $this->db->join('categories_entries', 'categories.id = categories_entries.category_id');
        $this->db->where('`categories_entries`.`entry_id`', $data['entry_id']);
        $Query = $this->db->get();
        $category_array = array();

        // Determine if a base path was passed to the path variable tag
        if (preg_match('/\{\{\s*path\s*=\s*[\'"](.*?)[\'"]\s*\}\}/', $content, $match))
        {
            if (isset($match[1]))
            {
                $base_path = $match[1];
            }
        }

        $results = $Query->result();
        $total_results = count($results);
        $count = 1;
        $parsed_content = '';

        foreach ($results as $Category)
        {
             $category_build = array(
                'category_id' => $Category->id,
                'title' => $Category->title,
                'target' => $Category->target,
                'id' => $Category->tag_id,
                'class' => $Category->class,
                'url_title' => $Category->url_title,
                'path' =>  site_url(( ! empty($base_path) ? trim($base_path, '/') : $this->CI->cms_base_route) . '/category/' . $Category->url_title),
            );

            // Check if the last element needs to be trimmed
            if ($total_results == $count && isset($parameters['backspace']))
            {
                $category_build['_content'] = substr($content, 0, $parameters['backspace'] * -1);
            } 

            $category_array[] = $category_build;

            $count++;
        }

        return $category_array;
    }

    // ------------------------------------------------------------------------

    /*
     * Date/Time Format Callback
     *
     * Callback to format a datetime
     *
     * @return void
     */
    public function date_format_callback($trigger, $parameters, $content, $data)
    {
        if (isset($data[$trigger]) && $data[$trigger] != '')
        {
            $date = $data[$trigger];

            if (isset($parameters['format']))
            {
                return date($parameters['format'], strtotime($date));
            }
            else
            {
                return $date;
            }
        }

        return '';
    }

    // ------------------------------------------------------------------------

    /*
     * Category Filter
     *
     * Queries entries based on a category specified in the URL
     *
     * @return void
     */
    private function _category_filter()
    {
        if ( ! empty($this->CI->cms_parameters))
        {
            $parameters = $this->CI->cms_parameters;
            $category_index = array_search('category', $parameters);

            /// Check that category and a url title was found
            if ($category_index === FALSE || ! isset($parameters[$category_index + 1]))
            {
                return;
            }

            // A valid year was found so filter entries
            $url_title = $parameters[$category_index + 1];

            $this->db->join('categories_entries', 'entries.id = categories_entries.entry_id');
            $this->db->join('categories', 'categories_entries.category_id = categories.id');
            $this->db->where('`categories`.`url_title`', $url_title);
        } 
    }

}
