<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Date_field extends Field_type
{
    function validate($data)
    {
        // Convert field to MYSQL date format
        if (isset($_POST['field_id_' . $data['Field']->id]) 
            && $_POST['field_id_' . $data['Field']->id] != '')
        {
            $_POST['field_id_' . $data['Field']->id] = date('Y-m-d', strtotime($_POST['field_id_' . $data['Field']->id]));
        }

        return TRUE;
    }

    function view($data)
    {
        $datepicker = '$(document).ready(function() {$(\'.datepicker\').datepicker();});';

        if ( ! in_array($datepicker, $this->template->scripts))
        {
            $this->template->add_script($datepicker);
        }

        return $this->load->view('date', $data, TRUE);
    }
}
