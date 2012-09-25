<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Default Unauthenticated Redirect
|--------------------------------------------------------------------------
|
| Directs where to redirect a user that has not logged in
|
*/
$config['unauthenticated_redirect'] = '/users/login';

/*
|--------------------------------------------------------------------------
| SSL Pages 
|--------------------------------------------------------------------------
|
|
*/
$config['ssl_pages'] = array();

/*
|--------------------------------------------------------------------------
| Parse views for tags
|--------------------------------------------------------------------------
|
| Disabling could make pages load slightly faster
|
*/
$config['parse_views'] = TRUE;

/*
|--------------------------------------------------------------------------
| MD5 Hash Secret
|--------------------------------------------------------------------------
|
| Used when generating encrypted md5 hashes
|
*/
$config['hash_secret'] = 'my_dirty_little_secret';
