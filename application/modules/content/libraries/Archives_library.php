<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Archives_library
{
    function __construct($config = array())
    {
        $this->CI =& get_instance();
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
        $this->sort = 'asc';
        $this->content_type = NULL;
        $this->path = NULL;
        $this->limit = NULL;
        $this->backspace = 0;
        $this->_content = '';
        $this->tag_id = null;
        $this->class = null;
        $this->nested = TRUE;
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
    }

    // ------------------------------------------------------------------------

    /*
     * Archive
     *
     * Queries and builds archive links
     *
     * @accecc public
     * @return string
     */
    function archive()
    {
        // Build Query
        $this->CI->db->select('MONTH(`created_date`) as `month`, YEAR(`created_date`) as `year`');
        $this->CI->db->from('entries');
        $this->CI->db->join('content_types', 'entries.content_type_id = content_types.id');
        $this->CI->db->group_by(array('month', 'year'));
        $this->CI->db->order_by('created_date', $this->sort);
        
        $content_types = explode('|', $this->content_type);
        $this->CI->db->where_in('content_types.short_name', $content_types);

        // Set a limit if limit passed
        if ($this->limit != '')
        {
            $this->CI->db->limit($this->limit);
        }

        $Query = $this->CI->db->get();
        
        $output = '';
        $array_count = 1;

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

        if ($this->nested)
        {
            $output = '<ul' . (($this->tag_id) ? ' id="' . $this->tag_id . '"' : '') . (($this->class) ? ' class="' . $this->class . '"' : '') . '>';
        }

        // Build result array
        foreach ($Query->result() as $Month_year)
        {
            $unix_time = strtotime($Month_year->month . '/01/' . $Month_year->year);
            $month = date('F', $unix_time);
            $month_num = date('m', $unix_time);
            $month_short = date('M', $unix_time);
            $year = date('Y', $unix_time);
            $year_short = date('y', $unix_time);

            if ($this->nested)
            {
                $output .= '<li>';
            }

            // Check if content was provided to use
            if ( ! empty($this->_content))
            {
                $month_year_array = array(
                    'month' => $month,
                    'month_num' => $month_num,
                    'month_short' => $month_short,
                    'year' => $year,
                    'year_short' => $year_short,
                    'path' =>  site_url($base_path . '/archive/' . $year . '/' . $month_num),
                );

                $content = $this->_content;

                // Backspace last element if backspace specified
                if ($array_count == $Query->num_rows() && $this->backspace)
                {
                    $content = substr($content, 0, $this->backspace * -1);
                }

                $output .= $this->CI->parser->parse_string($content, $month_year_array, true);
            }
            else
            {
                $output .= '<a href="' . site_url($base_path . '/archive/' . $year . '/' . $month_num) . '">' . $month . ' ' . $year . '</a>';
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
}
