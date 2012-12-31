<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Step4 extends CI_Controller 
{
    function index()
    {
        $data = array();
         
        $data['content'] = $this->load->view('step_4', $data, TRUE);
        $this->load->view('global', $data);
    }
}