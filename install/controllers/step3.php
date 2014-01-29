<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Step3 extends CI_Controller 
{
    public $errors = array();

    function index()
    {
        $data = array();

        $this->form_validation->set_rules('site_name', 'Site Name', 'trim|required');
        $this->form_validation->set_rules('server', 'Server', 'trim|required');
        $this->form_validation->set_rules('hostname', 'Database Host', 'trim|required');
        $this->form_validation->set_rules('username', 'Database Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Database Password', 'trim');
        $this->form_validation->set_rules('database', 'Database Name', 'trim|required');
        $this->form_validation->set_rules('port', 'Database Port', 'trim|required');
        $this->form_validation->set_rules('prefix', 'Database Prefix', 'trim');
        $this->form_validation->set_rules('email', 'Administrator Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('admin_password', 'Administrator Password', 'trim|required|min_length[6]');
        $this->form_validation->set_rules('confirm_admin_password', 'Confirm Administrator Password', 'trim|required|matches[admin_password]');
        $this->form_validation->set_rules('first_name', 'Administrator First Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Administrator Last Name', 'trim|required');

        if ($this->form_validation->run())
        {
            $config['db']['hostname'] = $this->input->post('hostname');
            $config['db']['username'] = $this->input->post('username');
            $config['db']['password'] = $this->input->post('password');
            $config['db']['database'] = $this->input->post('database');
            $config['db']['prefix'] = $this->input->post('prefix');
            $config['db']['port'] = $this->input->post('port');
            $config['server'] = $this->input->post('server');
            $this->load->library('installer', $config);

            try 
            {
                $this->installer->test_db_connection();
                $this->installer->write_ci_config();
                $this->installer->write_db_config();
                $this->installer->db_connect();
                $this->installer->import_schema();
                $this->installer->insert_administrator($this->input->post('email'), $this->input->post('admin_password'), $this->input->post('first_name'), $this->input->post('last_name'));
                $this->installer->update_site_name($this->input->post('site_name'));
                $this->installer->update_notification_email($this->input->post('email'));
                $this->installer->db_close();
                redirect('step4');
            }
            catch (Exception $e)
            {
                $this->installer->db_close();
                $this->errors[] = $e->getMessage();
            }
        }

        $data['rewrite_support'] = $this->test_mod_rewrite();
        $data['errors'] = $this->errors;
        $data['content'] = $this->load->view('step_3', $data, TRUE);
        $this->load->view('global', $data);
    }

    private function test_mod_rewrite() 
    {
        if (function_exists('apache_get_modules') && is_array(apache_get_modules()) && in_array('mod_rewrite', apache_get_modules())) 
        {
            return true;
        } 
        else if (getenv('HTTP_MOD_REWRITE') !== false)
        {
            return (getenv('HTTP_MOD_REWRITE') == 'On') ? true : false ;
        }
        else
        {
            return false;
        }
    }
}
