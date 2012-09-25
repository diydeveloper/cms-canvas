<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Checkbox_field extends Field_type
{
    function settings($data)
    {
        return $this->load->view('settings/checkbox', $data, TRUE);
    }

    function save($data)
    {
        // Convert data array to pipe delimited string
        if (isset($_POST['field_id_' . $data['Field']->id]) && is_array($_POST['field_id_' . $data['Field']->id]))
        {
            return implode('|', $_POST['field_id_' . $data['Field']->id]);
        }

        return NULL;
    }

    function view($data)
    {
        return $this->load->view('checkbox', $data, TRUE);
    }

    function output($data)
    {
        $Field = $data['Field'];
        $Entry = $data['Entry'];

        $value_array = array();

        if (isset($Entry->{'field_id_' . $Field->id}))
        {
            $this->parser->set_callback($Field->short_tag, array($this, 'checkbox_callback'));

            foreach(explode('|', $Entry->{'field_id_' . $Field->id}) as $value)
            {
                $value_array[] = array('item' => $value);
            }

            return $value_array;
        }

        return '';
    }

    function checkbox_callback($trigger, $parameters, $content, $data)
    {
        if ( ! isset($data[$trigger]))
        {
            return '';
        }

        $values = $data[$trigger];

        // Check if the last element needs to be trimmed
        if (is_array($values) && isset($parameters['backspace']))
        {
            $values[count($values) - 1]['_content'] = substr($content, 0, $parameters['backspace'] * -1);
        } 

        return $values;
    }
}
