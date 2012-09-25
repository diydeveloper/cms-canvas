<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Server_info extends Admin_Controller 
{

	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'Server Info'));
        $this->template->add_stylesheet('/application/modules/settings/assets/css/server_info.css');

        ob_start();
        phpinfo();
        $pinfo = ob_get_contents();
        ob_end_clean();
         
        $data['pinfo'] = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$pinfo);

        $this->template->view('admin/server_info', $data);
	}

}

