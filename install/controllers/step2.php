<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Step2 extends CI_Controller 
{
    public $errors = array();
    public $writable_dirs = array(
        'assets/cms' => FALSE,
        'assets/cms/cache' => FALSE,
        'assets/cms/uploads' => FALSE,
        'assets/cms/image-cache' => FALSE,
    );
    public $writable_subdirs = array(
        'assets/cms/cache/content_types' => FALSE,
        'assets/cms/cache/snippets' => FALSE,
        'assets/cms/cache/datamapper' => FALSE,
        'assets/cms/cache/entries' => FALSE,
        'assets/cms/cache/navigations' => FALSE,
        'assets/cms/cache/categories' => FALSE,
        'assets/cms/cache/settings' => FALSE,
        'assets/cms/uploads/images' => FALSE,
        'assets/cms/uploads/files' => FALSE,
        'assets/cms/uploads/.thumbs' => FALSE,
    );

    function index()
    {
        $data = array();
        clearstatcache();

        foreach ($this->writable_dirs as $path => $is_writable)
        {
            $this->writable_dirs[$path] = is_writable(CMS_ROOT . $path);
        }

        foreach ($this->writable_subdirs as $path => $is_writable)
        {
            if ( ! file_exists(CMS_ROOT . $path) || (file_exists(CMS_ROOT . $path) && is_writable(CMS_ROOT . $path)))
            {
                unset($this->writable_subdirs[$path]);
            }
        }

        if ($this->input->post())
        {
            if ($this->validate())
            {
                redirect('step3');
            }
        }

        $data['writable_dirs'] = array_merge($this->writable_dirs, $this->writable_subdirs);
        $data['errors'] = $this->errors;
        $data['content'] = $this->load->view('step_2', $data, TRUE);
        $this->load->view('global', $data);
    }

    private function validate()
    {
        if ( ! is_writable(CMS_ROOT . 'application/config/config.php'))
        {
            $this->errors[] =  CMS_ROOT . 'application/config/config.php is not writable.';
        }

        if ( ! is_writable(CMS_ROOT . 'application/config/database.php'))
        {
            $this->errors[] =  CMS_ROOT . 'application/config/database.php is not writable.';
        }

        $writable_dirs = array_merge($this->writable_dirs, $this->writable_subdirs);
        foreach ($writable_dirs as $path => $is_writable)
        {
            if ( ! $is_writable)
            {
                $this->errors[] = CMS_ROOT . $path . ' is not writable.';
            }
        }

        if (phpversion() < '5.1.6')
        {
            $this->errors[] = 'You need to use PHP 5.1.6 or greater.';
        }

        if ( ! ini_get('file_uploads'))
        {
            $this->errors[] = 'File uploads need to be enabled in your PHP configuration.';
        }

        if ( ! extension_loaded('mysql'))
        {
            $this->errors[] = 'The PHP MySQL extension is required.';
        }

        if ( ! extension_loaded('gd'))
        {
            $this->errors[] = 'The PHP GD extension is required.';
        }

        if ( ! extension_loaded('curl'))
        {
            $this->errors[] = 'The PHP cURL extension is required.';
        }

        if (empty($this->errors))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
}