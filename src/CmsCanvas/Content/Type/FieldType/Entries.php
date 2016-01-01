<?php 

namespace CmsCanvas\Content\Type\FieldType;

use View, Input, Content, Route;
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
     * @return \CmsCanvas\Content\Entry\RenderCollection
     */
    public function render()
    {
        return Content::entries($this->getEntrySettings());
    }

    /**
     * Queries and returns entries based on settings provided
     *
     * @return mixed
     */
    public function getEntrySettings()
    {
        $entrySettings = [];
        $currentRoute = Route::current();

        foreach ($this->settings as $key => $value) {
            $parameterNames = $currentRoute->parameterNames();

            foreach ($parameterNames as $parameterName) {
                $parameterTags["{{$parameterName}}"] = $currentRoute->parameter($parameterName);
            }

            if ($key == 'pagination_settings') {
                $entrySettings[$value] = $this->getSetting('per_page', 25);
            } else {
                $value = str_replace(array_keys($parameterTags), $parameterTags, $value);
                $entrySettings[$key] = $value;
            }
        }

        return $entrySettings;
    }
}