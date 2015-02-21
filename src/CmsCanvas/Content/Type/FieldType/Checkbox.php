<?php namespace CmsCanvas\Content\Type\FieldType;

use View, Input;
use CmsCanvas\Content\Type\FieldType;

class Checkbox extends FieldType {

    /**
     * Returns a view of additional settings for the checkbox field
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return View::make('cmscanvas::fieldType.checkbox.settings')
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
        $optionArray = array();
        foreach (explode("\n", $this->field->options) as $option)
        {
            $option = explode("=", $option, 2);
            $optionArray[trim($option[0])] = (count($option) == 2) ? trim($option[1]) : trim($option[0]);
        }

        return View::make('cmscanvas::fieldType.checkbox.input')
            ->with(array('fieldType' => $this, 'optionArray' => $optionArray));
    }

    /**
     * Sets the data class variable
     *
     * @param string $data
     * @return void
     */
    public function setData($data, $fromFormData = false)
    {
        // The data is already an array when being set from a form post
        if ($fromFormData)
        {
            $this->data = $data;
        }
        else
        {
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
        if (Input::get($this->getKey().'_checkbox') !== false 
            && Input::get($this->getKey()) === false
        )
        {
            return null;
        }

        // Convert data array to a JSON encoded string
        if ( ! empty($this->data))
        {
            return json_encode($this->data);
        }

        return null;
    }

    /**
     * Returns the rendered data for the field
     *
     * @return mixed
     */
    public function render()
    {
        $itemArray = array();

        foreach ($this->data as $value)
        {
            $itemArray[] = array('item' => $value);
        }

        return $itemArray;
    }

}