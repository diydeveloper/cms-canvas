<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

require_once APPPATH."third_party/MX/Config.php";

class MY_Config extends MX_Config
{
    /**
     * Site URL
     * Returns base_url . index_page [. uri_string]
     *
     * @access  public
     * @param   string  the URI string
     * @return  string
     */
    function site_url($uri = '')
    {
        $config_item = (isset($this->config['site_url'])) ? 'site_url' : 'base_url';

        if ($uri == '')
        {
            return $this->slash_item($config_item).$this->item('index_page');
        }

        if ($this->item('enable_query_strings') == FALSE)
        {
            $suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');
            return $this->slash_item($config_item).$this->slash_item('index_page').$this->_uri_string($uri).$suffix;
        }
        else
        {
            return $this->slash_item($config_item).$this->item('index_page').'?'.$this->_uri_string($uri);
        }
    }
}
