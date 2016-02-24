<?php 

namespace CmsCanvas\Content\Type\FieldType;

use Theme, Content;
use CmsCanvas\Content\Type\FieldType;
use Carbon\Carbon;

class DateTime extends FieldType {

    /**
     * Returns a view of additional settings for the datetime field
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return view('cmscanvas::fieldType.datetime.settings')
            ->with('fieldType', $this);
    }

    /**
     * Returns a view of the image field input
     *
     * @return \Illuminate\View\View
     */
    public function inputField()
    {
        $timezone = $this->getSetting('timezone');

        $data = ($this->data instanceof Carbon) 
            ? Content::userDate($this->data, 'd/M/Y h:i:s a', (($timezone == null) ? false : null))
            : '';

        return view('cmscanvas::fieldType.datetime.input')
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
        try {
            if ($rawRequestData) {
                $timezone = ($this->getSetting('timezone') == null) 
                    ? null
                    : auth()->user()->getTimezoneIdentifier();
                $data = Carbon::createFromFormat('d/M/Y h:i:s a', $data, $timezone);
                $data->setTimezone(config('app.timezone'));
            } else {
                $data = Carbon::createFromFormat('Y-m-d H:i:s', $data);
            }
        } catch (\Exception $e) {
            // Suppress carbon errors
            $data = '';
        }

        $this->data = $data;
    }

    /**
     * Returns the rendered data for the field
     *
     * @return mixed
     */
    public function renderContents()
    {
        $timezone = $this->getSetting('timezone');
        $data = $this->data;

        if ($data instanceof Carbon) {
            switch ($timezone) {
                case 'users_timezone':
                    $timezone = (auth()->check())
                        ? auth()->user()->getTimezoneIdentifier()
                        : config('cmscanvas::config.default_timezone');
                    $data->setTimezone($timezone);
                    break;

                case 'site_timezone':
                    $timezone = config('cmscanvas::config.default_timezone');
                    $data->setTimezone($timezone);
                    break;
            }
        }

        return $data;
    }

}