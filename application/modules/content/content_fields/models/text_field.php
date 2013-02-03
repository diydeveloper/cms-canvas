<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Text_field extends Field_type
{
    function settings()
    {
        $data = get_object_vars($this);
        
        return $this->load->view('settings/text', $data, TRUE);
    }

    function display_field()
    {
        $data = get_object_vars($this);
        
        return $this->load->view('text', $data, TRUE);
    }

    function output()
    {
        if ( ! isset($this->Field->settings['inline_editing']) || $this->Field->settings['inline_editing'])
        {
            return $this->_inline_editable(TRUE);
        }
        else
        {
            return $this->_inline_editable(FALSE);
        }
    }

    function parser_callback($tag, $attributes, $content, $data)
    {
        // This is a inline editable override used only for special cases
        if (isset($attributes['editable']) && str_to_bool($attributes['editable']))
        {
            return $this->_inline_editable(TRUE);
        }
        else
        {
            return $this->_inline_editable(FALSE);
        }
    }

    private function _inline_editable($editable)
    {
        if ($this->is_inline_editable() && $editable)
        {
            return '<div id="cc_field_' . $this->Entry->id . '_'. $this->Field->id  . '" class="cc_admin_editable cc_text_editable" contenteditable="true">{{ noparse }}' . $this->content . '{{ /noparse }}</div>';
        }
        else
        {
            return $this->content;
        }
    }
}
