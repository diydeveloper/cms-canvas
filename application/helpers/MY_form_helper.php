<?php
/*
* Einstein's Eyes custom form_helper
*/

// Override form_hidden to implement an id attribute
if ( ! function_exists('form_hidden'))
{
    function form_hidden($name, $value = '', $id = false)
    {
        if ( ! is_array($name))
        {
            return '<input type="hidden" id="'.($id ? $id : $name).'" name="'.$name.'" value="'.form_prep($value).'" />';
        }

        $form = '';

        foreach ($name as $name => $value)
        {
            $form .= "\n";
            $form .= '<input type="hidden"  id="'.($id ? $id : $name).'" name="'.$name.'" value="'.form_prep($value).'" />';
        }

        return $form;
    }
}

// Override validaton_errors to add error class to paragraph tags
if ( ! function_exists('validation_errors'))
{
	function validation_errors($prefix = '', $suffix = '')
	{
		if (FALSE === ($OBJ =& _get_validation_object()))
		{
			return '';
		}

        if($prefix == '' && $suffix == '')
        {
            $prefix = '<p class="error">';
            $suffix = '</p>';
        }

		return $OBJ->error_string($prefix, $suffix);
	}
}
