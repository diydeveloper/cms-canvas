<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller 
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $data = array();
        $data['breadcrumb'] = set_crumbs(array());

        if ($this->settings->ga_profile_id != '')
        {
            $this->template->add_stylesheet('application/modules/dashboard/assets/css/google_analytics.css');

            try
            {
                $this->load->model('google_analytics_model');
                $data['ga_data'] = $this->google_analytics_model->get_by_type('overview');
            }
            catch(Exception $e) 
            {
                $data['ga_data'] = '<p class="error">Unable to connect to Google Analytics. Please ensure your <a href="' . site_url(ADMIN_PATH . '/settings/general-settings') . '">analytic settings</a> are correct.</p>';
            }

            $data['month_year'] = array();

            for($i = 0; $i < 12; $i++)
            {
                $unix_month = strtotime("-$i months");
                $data['month_year'][$unix_month] = date('F Y', $unix_month);
            }
        }

        $this->template->view('admin/index', $data);
    }

    function get_ga_data()
    {
        if ( ! is_ajax())
        {
            return show_404();
        }

        $this->load->model('google_analytics_model');
        $this->google_analytics_model->set_month($this->input->post('month_year'));
        echo $this->google_analytics_model->get_by_type($this->input->post('ga_data_type'));
    }
}
