<?php namespace CmsCanvas\Routing;

use Theme;
use CmsCanvas\Routing\Controller;

class AdminController extends Controller {

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
    protected $layout = 'default';

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
