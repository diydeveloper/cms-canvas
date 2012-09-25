<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {

    function __construct()
    {
        parent::__construct();
        log_message('debug', 'MY_Exceptions Class Initialized');
    }
    
	function show_404($page = '', $log_error = TRUE)
    {
		set_status_header('404');

        $this->CI =& get_instance();
        $error_uri = ($this->CI->router->routes['404_override']);

		// By default we log this, but allow a dev to skip it
		if ($log_error)
		{
			log_message('error', '404 Page Not Found --> '.$page);
		}

        // get the CI base URL
        $this->config =& get_config();
        $base_url = $this->config['base_url'];     
        
        // Get current session
        $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
        
        // Close current session
        session_write_close();

        // create new cURL resource
        $ch = curl_init();
        
        // set URL and other options
        curl_setopt($ch, CURLOPT_URL, $base_url . $error_uri);
        curl_setopt($ch, CURLOPT_HEADER, 0); // note: the 404 header is already set in the error controller
        curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );
        
        // pass URL to the browser
        curl_exec($ch);
        
        // close cURL resource, and free up system resources
        curl_close($ch);
    }
   
}

/* End of file MY_Exceptions.php */
/* Location: ./system/application/libraries/MY_Exceptions.php */ 
