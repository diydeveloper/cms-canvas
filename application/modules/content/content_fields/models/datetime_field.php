<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Datetime_field extends Field_type
{
    function display_field()
    {
        $data = get_object_vars($this);
        
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


    function save()
    {
        if ($this->content != '')
        {
            return date('Y-m-d H:i:s', strtotime($this->content));
        }
        else
        {
            return null;
        }
    }
}
