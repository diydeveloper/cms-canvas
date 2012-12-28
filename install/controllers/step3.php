<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Step3 extends CI_Controller 
{
    public $errors = array();

    function index()
    {
        $data = array();
        $this->load->library('installer');

        $this->form_validation->set_rules('hostname', 'Database Host', 'trim|required');
        $this->form_validation->set_rules('username', 'Database Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Database Password', 'trim|required');
        $this->form_validation->set_rules('database', 'Database Name', 'trim|required');
        $this->form_validation->set_rules('port', 'Database Port', 'trim|required');
        $this->form_validation->set_rules('prefix', 'Database Prefix', 'trim');
        $this->form_validation->set_rules('email', 'Administrator Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('admin_password', 'Administrator Password', 'trim|required');

        if ($this->form_validation->run())
        {
            $hostname = $this->input->post('hostname');
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $database = $this->input->post('database');
            $prefix = $this->input->post('prefix');
            $port = $this->input->post('port');

            try 
            {
                $db_driver = $this->installer->test_db_connection($hostname, $username, $password, $database, $port);
                $this->installer->write_db_config($hostname, $username, $password, $database, $port, $prefix, $db_driver);
            }
            catch (Exception $e)
            {
                $this->errors[] = $e->getMessage();
            }
        }

        $data['errors'] = $this->errors;
        $data['content'] = $this->load->view('step_3', $data, TRUE);
        $this->load->view('global', $data);
    }
}