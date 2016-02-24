<?php 

namespace CmsCanvas\Content\Type\FieldType;

use Request;
use CmsCanvas\Content\Type\FieldType;

class Checkbox extends FieldType {

    /**
     * Returns a view of additional settings for the checkbox field
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return view('cmscanvas::fieldType.checkbox.settings')
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

        return view('cmscanvas::fieldType.checkbox.input')
            ->with(['fieldType' => $this, 'optionArray' => $optionArray]);
    }

    /**
     * Sets the data class variable
     *
     * @param  string $data
     * @param  bool $rawRequestData
     * @return void
     */
    public function setData($data, $rawRequestData = false)
    {
        // The data is already an array when being set from a form post
        if ($rawRequestData) {
            $this->data = $data;
        } else {
            $this->data = (array) @json_decode($data, true);
        }
    }

    /**
     * Returns a json encoded array to be saved to the database
     *
     * @return string
     */
    public function getSaveData()
    {
        // If the hidden checkbox indicator field is posted but the field is not, 
        // this means that no checkboxes were selected
        if (Request::input($this->getKey().'_checkbox') !== false 
            && Request::input($this->getKey()) === false
        ) {
            return null;
        }

        // Convert data array to a JSON encoded string
        if ( ! empty($this->data)) {
            return json_encode($this->data);
        }

        return null;
    }

    /**
     * Returns the rendered data for the field
     *
     * @return mixed
     */
    public function renderContents()
    {
        $itemArray = [];

        foreach ($this->data as $value) {
            $itemArray[] = ['item' => $value];
        }

        return $itemArray;
    }

}