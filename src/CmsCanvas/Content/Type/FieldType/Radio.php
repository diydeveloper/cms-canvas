<?php 

namespace CmsCanvas\Content\Type\FieldType;

use CmsCanvas\Content\Type\FieldType;

class Radio extends FieldType {

    /**
     * Returns a view of additional settings for the checkbox field
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return view('cmscanvas::fieldType.radio.settings')
            ->with('fieldType', $this);
    }

    /**
     * Returns a view of the checkbox field input
     *
     * @return \Illuminate\View\View
     */
    public function inputField()
    {
        // Build options array
        $optionArray = [];
        foreach (explode("\n", $this->field->options) as $option) {
            $option = explode("=", $option, 2);
            $optionArray[trim($option[0])] = (count($option) == 2) ? trim($option[1]) : trim($option[0]);
        }

        return view('cmscanvas::fieldType.radio.input')
            ->with(['fieldType' => $this, 'optionArray' => $optionArray]);
    }

}