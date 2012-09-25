<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Theme_editor extends Admin_Controller 
{
	function __construct()
	{
		parent::__construct();	
	}

    function index()
    {
        $data = array();
        $data['breadcrumb'] = set_crumbs(array(current_url() => 'Theme Editor'));
        $theme_dir = FCPATH . 'themes/' . $this->settings->theme . '/';

        $data['files_found'] = TRUE;
        $data['file_readable'] = TRUE;
        $data['file_writable'] = TRUE;

        $this->load->helper('file');
        $this->load->model('theme_editor_model');
        $this->template->add_stylesheet('/application/modules/settings/assets/css/theme_editor.css'); 

        // Load CodeMirror Scripts
        $this->template->add_package('codemirror');

        // Get theme files
        $data['layouts'] = $this->theme_editor_model->get_theme_files('{php, html}', 'views/layouts');
        $data['partials'] = $this->theme_editor_model->get_theme_files('{php, html}', 'views/partials');
        $data['stylesheets'] = $this->theme_editor_model->get_theme_files('css');
        $data['javascripts'] = $this->theme_editor_model->get_theme_files('js');

        // Read selected file
        if ($hash = $this->uri->segment(5))
        {
            $data['file'] = $file = url_base64_decode($hash);
        }
        else
        {
            // Check if assets/css/style.css exists and load it first if no other file specified
            if (isset($data['stylesheets']['assets/css/style.css']))
            {
                $data['file'] = $file = url_base64_decode($data['stylesheets']['assets/css/style.css']['hash']);
            }
            else
            {
                // There was no style.css so load first file found if any
                foreach (array('stylesheets', 'layouts', 'partials', 'javascripts') as $theme_type)
                {
                    if ( ! empty($data[$theme_type]))
                    {
                        $file_array = current($data[$theme_type]);
                        $data['file'] = $file = url_base64_decode($file_array['hash']);
                        break;
                    }
                }
            }
        }

        if ( ! empty($file))
        {
            // Check if the file exists and is readable
            if (is_readable($theme_dir. $file))
            {
                // Read the file
                $data['code'] = read_file($theme_dir. $file);

                // Check if file is writable
                if ( ! is_writable($theme_dir. $file))
                {
                    $data['file_writable'] = FALSE;
                }

                // Determine CodeMirror mode from extension
                switch (pathinfo($file, PATHINFO_EXTENSION))
                {
                    case "css":
                        $data['mode'] = 'text/css';
                        break;
                    case "js":
                        $data['mode'] = 'text/javascript';
                        break;
                    default:
                        $data['mode'] = 'application/x-httpd-php';
                        break;
                }
            }
            else
            {
                // Flag file as not found or not readable
                $data['file_readable'] = FALSE;
            }
        }
        else
        {
            // No theme files were found
            $data['files_found'] = FALSE;
        }

        // Form validation
        $this->form_validation->set_rules('code', 'Text Area', 'required');

        if ($this->form_validation->run() == TRUE)
        {
            // Write post to file
            if (write_file($theme_dir . $file, $this->input->post('code')))
            {
                $this->session->set_flashdata('message', '<p class="success">File saved successfully.</p>');
            }
            else
            {
                $this->session->set_flashdata('message', '<p class="error">Unable to write to file.</p>');
            }

            redirect(current_url());
        }

        $this->template->view('admin/theme_editor', $data);
    }
}
