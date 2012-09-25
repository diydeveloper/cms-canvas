<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// The name of the directory where templates are located.
//$config['template_dir'] = dirname(FCPATH) . '/../application/views/';

// The directory where compiled templates are located
$config['parser_compile_dir']   = FCPATH . '/cache/';

//This tells Dwoo whether or not to cache the output of the templates to the $cache_dir.
//$config['caching']      = 0;
$config['parser_cache_dir']     = FCPATH . '/cache/';
$config['parser_cache_time']    = 0;
