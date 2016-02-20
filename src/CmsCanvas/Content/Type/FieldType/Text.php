<?php 

namespace CmsCanvas\Content\Type\FieldType;

use View;
use CmsCanvas\Content\Type\FieldType;

class Text extends FieldType {

    /**
     * Returns a view of the text field input
     *
     * @return \Illuminate\View\View
     */
    public function inputField()
    {
        return View::make('cmscanvas::fieldType.text.input')
            ->with('fieldType', $this);
    }

    /**
     * Returns editable content
     *
     * @return string
     */
    public function renderEditableContents()
    {
        return '<div id="'.$this->getInlineEditableKey().'" class="cc_admin_editable cc_text_editable" contenteditable="true">'
            .$this->renderContents()
            .'</div>';
    }

}