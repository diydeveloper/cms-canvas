<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Date_field extends Field_type
{
    function display_field()
    {
        $data = get_object_vars($this);

        $datepicker = '$(document).ready(function() {$(\'.datepicker\').datepicker();});';

        if ( ! in_array($datepicker, $this->template->scripts))
        {
            $this->template->add_script($datepicker);
        }

        return $this->load->view('date', $data, TRUE);
    }

    function save()
    {
        if ($this->content != '')
        {
            return date('Y-m-d', strtotime($this->content));
        }
        else
        {
            return null;
        }
    }
}
