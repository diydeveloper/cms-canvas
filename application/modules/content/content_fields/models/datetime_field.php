<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Datetime_field extends Field_type
{
    function validate($data)
    {
        // Convert field to MYSQL date format
        if (isset($_POST['field_id_' . $data['Field']->id]) 
            && $_POST['field_id_' . $data['Field']->id] != '')
        {
            $_POST['field_id_' . $data['Field']->id] = date('Y-m-d H:i:s', strtotime($_POST['field_id_' . $data['Field']->id]));
        }

        return TRUE;
    }

    function view($data)
    {
       /* May need in the future
        * Currently javascript is always loaded for date_created

        $datetimepicker = '$(document).ready(function() {$(\'.datepicker\').datetimepicker({ showSecond: true, timeFormat: \'hh:mm:ss tt\', ampm: true });});';

        if ( ! in_array($datetimepicker, $this->template->scripts))
        {
            $this->template->add_script($datetimepicker);
        }
         */

        return $this->load->view('datetime', $data, TRUE);
    }
}
