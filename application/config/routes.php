<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

// Redirect to the installer if the installed config item is set to false or is missing
if ( ! config_item('installed'))
{
    if (file_exists(CMS_ROOT . 'install'))
    {
        header('Location: /install/index.php');
    }
    else
    {
        echo "Could not find install directory.";
    }
    exit;
}

$route['default_controller'] = "content/pages";
$route['404_override'] = 'content/pages';

$route[ADMIN_PATH] = "dashboard/admin/dashboard";
$route[ADMIN_PATH . '/([a-zA-Z_-]+)/(:any)'] = "$1/admin/$2";
$route[ADMIN_PATH . '/([a-zA-Z_-]+)'] = "$1/admin/$1";


// Special Case Routes
$route[ADMIN_PATH . '/users/login'] = "users/login";
$route[ADMIN_PATH . '/users/forgot-password'] = "users/forgot-password";

/* End of file routes.php */
/* Location: ./application/config/routes.php */
