<?php 

namespace CmsCanvas\Content\Type\FieldType;

use CmsCanvas\Content\Type\FieldType;

class Textarea extends FieldType {

    /**
     * Returns a view of additional settings for the checkbox field
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return view('cmscanvas::fieldType.textarea.settings')
            ->with('fieldType', $this);
    }

    /**
     * Returns a view of the text field input
     *
     * @return \Illuminate\View\View
     */
    public function inputField()
    {
        return view('cmscanvas::fieldType.textarea.input')
            ->with('fieldType', $this);
    }

}