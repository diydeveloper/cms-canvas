<?php 

namespace CmsCanvas\Content\Type\FieldType;

use View, Input;
use CmsCanvas\Content\Type\FieldType;

class Entries extends FieldType {

    /**
     * Returns a view of additional settings for the checkbox field
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return View::make('cmscanvas::fieldType.entries.settings')
            ->with('fieldType', $this);
    }

    /**
     * No input field needed for entries
     *
     * @return null
     */
    public function inputField()
    {
        return null;
    }

    /**
     * Queries and returns entries based on settings provided
     *
     * @return mixed
     */
    public function render()
    {
        return Content::entries();
    }
}