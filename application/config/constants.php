<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Admin Path
|--------------------------------------------------------------------------
|
*/
define('ADMIN_PATH', 'sitemin');

/*
|--------------------------------------------------------------------------
| Group Types
|--------------------------------------------------------------------------
|
*/
define('USER',          'user');
define('ADMINISTRATOR', 'administrator');
define('SUPER_ADMIN',   'super_admin');

/*
|--------------------------------------------------------------------------
| CMS Cache Directory
|--------------------------------------------------------------------------
|
*/
define('IMAGE_CACHE',   '/assets/cms/cache/images');

/*
|--------------------------------------------------------------------------
| User Data Storage
|--------------------------------------------------------------------------
|
*/
define('USER_DATA',   '/assets/userdata/');

/*
|--------------------------------------------------------------------------
| Packages
|--------------------------------------------------------------------------
|
*/
$packages = array(
    'jquery' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/jquery.min.js',
        ),
    ),
    'jquerytools' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/jquery.tools.min.js',
        ),
    ),
    'labelify' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/jquery.labelify.js',
        ),
    ),
    'tablednd' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/jquery.tablednd_0_5.js',
        ),
    ),
    'superfish' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/superfish.js',
        ),
    ),
    'zclip' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/zclip/jquery.zclip.min.js',
        ),
    ),
    'jquerycycle' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/jquery.cycle.all.min.js',
        ),
    ),
    'tinymce' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/tiny_mce/tiny_mce.js',
        ),
    ),
    'ckeditor' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/ckeditor/ckeditor.js',
        ),
    ),
    'ck_jq_adapter' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/ckeditor/adapters/jquery.js',
        ),
    ),
    'fancybox' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/fancybox/jquery.fancybox-1.3.4.pack.js',
        ),
        'stylesheet' => array(
            '/application/themes/admin/assets/js/fancybox/jquery.fancybox-1.3.4.css',
        ),
    ),
    'nestedSortable' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/nested_sortable/jquery.ui.nestedSortable.js',
        ),
        'stylesheet' => array(
            '/application/themes/admin/assets/js/nested_sortable/jquery.ui.nestedSortable.css',
        ),
    ),
    'codemirror' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/codemirror-2.25/lib/codemirror.js',
            '/application/themes/admin/assets/js/codemirror-2.25/mode/xml/xml.js',
            '/application/themes/admin/assets/js/codemirror-2.25/mode/javascript/javascript.js',
            '/application/themes/admin/assets/js/codemirror-2.25/mode/css/css.js',
            '/application/themes/admin/assets/js/codemirror-2.25/mode/clike/clike.js',
            '/application/themes/admin/assets/js/codemirror-2.25/mode/php/php.js',
        ),
        'stylesheet' => array(
            '/application/themes/admin/assets/js/codemirror-2.25/lib/codemirror.css',
        ),
    ),
    'admin_jqueryui' => array(
        'javascript' => array(
            '/application/themes/admin/assets/js/jqueryui/jquery-ui-1.8.17.custom.min.js',
            '/application/themes/admin/assets/js/jquery-ui-timepicker-addon.js',
        ),
        'stylesheet' => array(
            '/application/themes/admin/assets/js/jqueryui/smoothness/jquery-ui-1.8.17.custom.css',
        ),
    ),
);

define('PACKAGES', serialize($packages));

/*
|--------------------------------------------------------------------------
| States
|--------------------------------------------------------------------------
|
*/
$states = array(
                ''  =>"",
                'AL'=>"Alabama",
                'AK'=>"Alaska", 
                'AZ'=>"Arizona", 
                'AR'=>"Arkansas", 
                'CA'=>"California", 
                'CO'=>"Colorado", 
                'CT'=>"Connecticut", 
                'DE'=>"Delaware", 
                'DC'=>"District Of Columbia", 
                'FL'=>"Florida", 
                'GA'=>"Georgia", 
                'HI'=>"Hawaii", 
                'ID'=>"Idaho", 
                'IL'=>"Illinois", 
                'IN'=>"Indiana", 
                'IA'=>"Iowa", 
                'KS'=>"Kansas", 
                'KY'=>"Kentucky", 
                'LA'=>"Louisiana", 
                'ME'=>"Maine", 
                'MD'=>"Maryland", 
                'MA'=>"Massachusetts", 
                'MI'=>"Michigan", 
                'MN'=>"Minnesota", 
                'MS'=>"Mississippi", 
                'MO'=>"Missouri", 
                'MT'=>"Montana",
                'NE'=>"Nebraska",
                'NV'=>"Nevada",
                'NH'=>"New Hampshire",
                'NJ'=>"New Jersey",
                'NM'=>"New Mexico",
                'NY'=>"New York",
                'NC'=>"North Carolina",
                'ND'=>"North Dakota",
                'OH'=>"Ohio", 
                'OK'=>"Oklahoma", 
                'OR'=>"Oregon", 
                'PA'=>"Pennsylvania", 
                'RI'=>"Rhode Island", 
                'SC'=>"South Carolina", 
                'SD'=>"South Dakota",
                'TN'=>"Tennessee", 
                'TX'=>"Texas", 
                'UT'=>"Utah", 
                'VT'=>"Vermont", 
                'VA'=>"Virginia", 
                'WA'=>"Washington", 
                'WV'=>"West Virginia", 
                'WI'=>"Wisconsin", 
                'WY'=>"Wyoming"
            );
define('STATES', serialize($states));


/*
|--------------------------------------------------------------------------
| Admin Access Area Options
|--------------------------------------------------------------------------
|
*/
$admin_access_options = array(
    ADMIN_PATH . '/content/entries'      => 'Content / Entries',
    ADMIN_PATH . '/navigations'           => 'Content / Navigations',
    ADMIN_PATH . '/galleries'            => 'Content / Galleries',
    ADMIN_PATH . '/users'                => 'Users',
    ADMIN_PATH . '/users/groups'         => 'User Groups',
    ADMIN_PATH . '/content/types'        => 'Tools / Content Types',
    ADMIN_PATH . '/categories'           => 'Tools / Categories',
    ADMIN_PATH . '/settings/theme-editor' => 'Tools / Theme Editor',
    ADMIN_PATH . '/settings/general-settings' => 'General Settings',
    ADMIN_PATH . '/settings/clear-cache' => 'Settings / Clear Cache',
    ADMIN_PATH . '/settings/server-info' => 'Settings / Server Info',
);

define('ADMIN_ACCESS_OPTIONS', serialize($admin_access_options));

/*
|--------------------------------------------------------------------------
| Admin Missing Image
|--------------------------------------------------------------------------
|
*/
define('ADMIN_NO_IMAGE', '/application/themes/admin/assets/images/no_image.jpg');

/* End of file constants.php */
/* Location: ./system/application/config/constants.php */

