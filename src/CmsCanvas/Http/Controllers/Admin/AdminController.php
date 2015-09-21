<?php 

namespace CmsCanvas\Http\Controllers\Admin;

use Theme;
use CmsCanvas\Http\Controllers\Controller;

class AdminController extends Controller {

    /**
     * The default theme to be used with this controller
     *
     * @var string
     */
    protected $themeName = 'admin';

    /**
     * The default layout to be used with this controller
     *
     * @var string
     */
    protected $layoutName = 'default';

    /**
     * Load javascript packages for the admin panel
     *
     * @return void
     */
    public function __construct()
    {
        // Load jQuery by default
        Theme::addPackage(['jquery', 'jquerytools', 'admin_jqueryui']);

        // Set up the session for KCFinder
        if (session_id() == '') {
            @session_start();
        }
        $_SESSION['KCFINDER'] = [
            'disabled' => false
        ];
    }

}
