<?php namespace CmsCanvas\Http\Controllers\Admin;

use View, Theme, Config, Admin;
use CmsCanvas\Http\Controllers\Admin\AdminController;

class DashboardController extends AdminController {

    /**
     * Display admin dashboard screen
     *
     * @return View
     */
    public function getDashboard()
    {
        $content = View::make('cmscanvas::admin.dashboard.dashboard');

        if (Config::get('cmscanvas::config.ga_profile_id'))
        {
            // $this->template->add_stylesheet('application/modules/dashboard/assets/css/google_analytics.css');

            // try
            // {
            //     $this->load->model('google_analytics_model');
            //     $data['ga_data'] = $this->google_analytics_model->get_by_type('overview');
            // }
            // catch(Exception $e) 
            // {
            //     $data['ga_data'] = '<p class="error">Unable to connect to Google Analytics. Please ensure your <a href="' . site_url(ADMIN_PATH . '/settings/general-settings') . '">analytic settings</a> are correct.</p>';
            // }

            // $data['month_year'] = array();

            // for($i = 0; $i < 12; $i++)
            // {
            //     $unix_month = strtotime("-$i months");
            //     $data['month_year'][$unix_month] = date('F Y', $unix_month);
            // }
        }

        $this->layout->breadcrumbs = array();
        $this->layout->content = $content;
    }

}