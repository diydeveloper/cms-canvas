<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Template_plugin extends Plugin
{
    public function set_layout()
    {
        $this->template->set_layout($this->attribute('layout', 'default'));
    }

    public function add_javascript()
    {
        $this->template->add_javascript($this->attribute('file'));
    }

    public function add_stylesheet()
    {
        $this->template->add_stylesheet($this->attribute('file'));
    }

    public function set_meta_title($data)
    {
        // Recieve inherited data passed to plugin from parent plugin
        $CI =& get_instance();
        $CI->load->library('parser');

        $content = $this->attribute('content', $this->content());
        $parsed_content = $CI->parser->parse_string($content, $data, TRUE);
        $this->template->set_meta_title(trim($parsed_content));
    }

    public function set_meta_description($data)
    {
        // Recieve inherited data passed to plugin from parent plugin
        $CI =& get_instance();
        $CI->load->library('parser');

        $content = $this->attribute('content', $this->content());
        $parsed_content = $CI->parser->parse_string($content, $data, TRUE);
        $this->template->set_meta_description(trim($parsed_content));
    }

    public function set_meta_keywords($data)
    {
        // Recieve inherited data passed to plugin from parent plugin
        $CI =& get_instance();
        $CI->load->library('parser');

        $content = $this->attribute('content', $this->content());
        $parsed_content = $CI->parser->parse_string($content, $data, TRUE);
        $this->template->set_meta_keywords(trim($parsed_content));
    }

    public function javascripts()
    {
        return $this->template->javascripts();
    }

    public function stylesheets()
    {
        return $this->template->stylesheets();
    }

    public function metadata()
    {
        return $this->template->metadata();
    }

    public function analytics()
    {
        return $this->template->analytics();
    }

    public function page_head()
    {
        return $this->template->page_head();
    }

    public function head()
    {
        return $this->template->head();
    }

    public function xml_output()
    {
        return xml_output();
    }
}

