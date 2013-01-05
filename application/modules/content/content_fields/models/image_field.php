<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Image_field extends Field_type
{
    function settings()
    {
        $data = get_object_vars($this);
        
        return $this->load->view('settings/image', $data, TRUE);
    }

    function display_field()
    {
        $data = get_object_vars($this);
        
        $this->template->add_javascript('/application/modules/content/content_fields/assets/js/image.js');
        return $this->load->view('image', $data, TRUE);
    }

    function output()
    {
        $settings = $this->Field->settings;

        if ($settings['output'] == 'image')
        {
            if ( ! empty($settings['max_width']) || ! empty($settings['max_height']))
            {
                $max_width = ( ! empty($settings['max_width'])) ? $settings['max_width'] : 0;
                $max_height = ( ! empty($settings['max_height'])) ? $settings['max_height'] : 0;
                $crop = ($settings['crop'] == '1') ? TRUE : FALSE;
                $image_src = image_thumb($this->content, $max_width, $max_height, $crop);
            }
            else
            {
                $image_src = base_url($this->content);
            }

            $id = ($settings['id']) ? ' id="' . $settings['id'] . '"' : '';

             if ($this->is_inline_editable())
             {
                 $this->template->add_javascript('/application/modules/content/content_fields/assets/js/image_inline_editable.js');
                 $_SESSION['KCFINDER'] = array();
                 $_SESSION['KCFINDER']['disabled'] = false;
                 $_SESSION['isLoggedIn'] = true;

                 $class = ($settings['class']) ? ' class="' . $settings['class'] . ' cc_image_editable' . '"' : ' class="cc_image_editable"';

                 return '<img' . $id . $class . ' src="' . $image_src . '" /><input id="cc_field_' . $this->Entry->id . '_'. $this->Field->id  . '" class="cc_hidden_editable" type="hidden" value="' . $this->content . '" />';
             }
             else
             {
                 $class = ($settings['class']) ? ' class="' . $settings['class'] . '"' : '';
                 return '<img' . $id . $class . ' src="' . $image_src . '" />';
             }
        }
        else
        {
            return base_url($this->content);
        }
    }
}
