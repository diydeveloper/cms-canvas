<?php 

namespace CmsCanvas\Http\Controllers\Admin;

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

        $this->layout->breadcrumbs = [];
        $this->layout->content = $content;
    }

}