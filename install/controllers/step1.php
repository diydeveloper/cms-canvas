<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Step1 extends CI_Controller 
{
	function index()
	{
        $data = array();

        $this->form_validation->set_rules('accept', 'Agree to License', 'trim|required');
        $this->form_validation->set_message('required', 'You must agree to the license before you can install CMS Canvas!');

        if ($this->form_validation->run())
        {
            redirect('step2');
        }

        $data['content'] = $this->load->view('step_1', $data, TRUE);
        $this->load->view('global', $data);
	}
}