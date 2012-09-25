<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Protected_Controller extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

        $this->secure
            ->unauthenticated_redirect('users/login')
            ->require_auth();

        // Load jQuery by default
        $this->template->add_package('jquery');
	}
}
