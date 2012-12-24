<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Clear_cache extends Admin_Controller 
{

	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'Clear Cache'));

        $this->form_validation->set_rules('cache[]', 'Cache', 'required');

        if ($this->form_validation->run() == TRUE)
        {
            $this->load->library('cache');

            foreach ($this->input->post('cache') as $cache => $value)
            {
                // Clear cache
                switch ($cache)
                {
                    case "entries":
                        $this->cache->delete_all('entries');
                        break;
                    case "content_types":
                        $this->cache->delete_all('content_types');
                        break;
                    case "images":
                        $this->cache->delete_all('images');
                        break;
                    case "navigations":
                        $this->cache->delete_all('navigations');
                        break;
                    case "categories":
                        $this->cache->delete_all('categories');
                        break;
                    case "settings":
                        $this->cache->delete_all('settings');
                        break;
                    case "datamapper":
                        $this->cache->delete_all('datamapper');
                        break;
                }
            }

            // Set a success message
            $this->session->set_flashdata('message', '<p class="success">Cache succcessfully cleared.</p>');
            redirect(current_url());
        }

        $this->template->view('admin/clear_cache', $data);
	}

}

