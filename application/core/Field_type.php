<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Field_type extends CI_Model
{
    public function view($data)
    {
        return '';
    }

    public function output($data) 
    { 
        $Field = $data['Field'];
        $Entry = $data['Entry'];

        return isset($Entry->{'field_id_' . $Field->id}) ? $Entry->{'field_id_' . $Field->id} : '';
    }

    public function save($data) 
    { 
        $Field = $data['Field'];
        return $this->input->post('field_id_' . $Field->id);
    }

    public function settings() 
    { 
        return '';
    }

    public function validate()
    {
        return TRUE;
    }
}
