<?php 

namespace CmsCanvas\Content\Type\FieldType;

use Theme, Content;
use Carbon\Carbon;
use CmsCanvas\Content\Type\FieldType;

class Date extends FieldType {

    /**
     * Returns a view of the image field input
     *
     * @return \Illuminate\View\View
     */
    public function inputField()
    {
        Theme::addInlineScript('$(document).ready(function() {$(\'.datepicker\').datepicker({dateFormat: \'d/M/yy\'});});', true);

        $data = ($this->data instanceof Carbon) 
            ? Content::userDate($this->data, 'd/M/Y', false)
            : '';

        return view('cmscanvas::fieldType.date.input')
            ->with(['fieldType' => $this, 'data' => $data]);
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
        $format = ($rawRequestData) ? 'd/M/Y' : 'Y-m-d H:i:s';

        try {
            $data = Carbon::createFromFormat($format, $data);
        } catch (\Exception $e) {
            // Suppress carbon errors
            $data = '';
        }

        $this->data = $data;
    }

}