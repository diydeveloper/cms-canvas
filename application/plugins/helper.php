<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Helper_plugin extends Plugin
{
    public function date()
    {
        if ($this->attribute('date') != '')
        {
            return date($this->attribute('format', 'm/d/Y'), strtotime($this->attribute('date')));
        }
        else
        {
            return date($this->attribute('format', 'm/d/Y'));
        }
    }

    public function site_url()
    {
        return site_url($this->attribute('path', ''));
    }

    public function base_url()
    {
        return base_url($this->attribute('path', ''));
    }

    public function current_url()
    {
        return current_url();
    }

    public function image_thumb()
    {
        return image_thumb($this->attribute('image'), $this->attribute('width', 0), $this->attribute('height', 0), $this->attribute('crop', FALSE));
    }

    public function uri_segment()
    {
        $CI =& get_instance();
        return $CI->uri->segment($this->attribute('segment'));
    }

    public function ellipsis($data)
    {
        // Recieve inherited data passed to plugin from parent plugin
        $CI =& get_instance();
        $CI->load->library('parser');
        $CI->load->helper('text');

        $content = $this->content();
        $parsed_content = $CI->parser->parse_string($content, $data, TRUE);
        return ellipsize($parsed_content, $this->attribute('length'));
    }

    public function code($data)
    {
        $content = $this->content();
        $content = str_replace('{{', '&#123;&#123;', $content);
        $content = str_replace('}}', '&#125;&#125;', $content);
        return $content;
    }
}

