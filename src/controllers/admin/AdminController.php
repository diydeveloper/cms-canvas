<?php namespace CmsCanvas\Controllers\Admin;

use Theme;
use CmsCanvas\Controllers\BaseController;

class AdminController extends BaseController {

    /**
     * The default theme to be used with this controller
     *
     * @var string
     */
    protected $theme = 'admin';

    /**
     * The default layout to be used with this controller
     *
     * @var string
     */
    protected $layout = 'layouts.default';

    /**
     * Load javascript packages for the admin panel
     *
     * @return void
     */
    public function __construct()
    {
        // Load jQuery by default
        Theme::addPackage(array('jquery', 'jquerytools', 'admin_jqueryui'));

        // Set up the session for KCFinder
        if (session_id() == '') {
            @session_start();
        }
        $_SESSION['KCFINDER'] = array(
            'disabled' => false
        );
    }

}
