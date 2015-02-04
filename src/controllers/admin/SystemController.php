<?php namespace CmsCanvas\Controllers\Admin;

use View, Request, stdClass, Theme, Config, Input;
use CmsCanvas\Routing\AdminController;
use CmsCanvas\Models\Setting;
use CmsCanvas\Models\Content\Entry;

class SystemController extends AdminController {

    /**
     * Display general settings
     *
     * @return View
     */
    public function getGeneralSettings()
    {
        $content = View::make('cmscanvas::admin.system.generalSettings');

        $settingItems = Setting::all();
        $settings = new stdClass;
        foreach ($settingItems as $settingItem) {
            $settings->{$settingItem->setting} = $settingItem->value;
        }

        $content->settings = $settings;
        $content->themes = Theme::getThemes();
        $content->layouts = Theme::getThemeLayouts(Config::get('cmscanvas::config.theme'));
        // @todo add paginated entry search
        $content->entries = Entry::orderBy('title', 'asc')->get();

        $this->layout->breadcrumbs = array(Request::path() => 'General Settings');
        $this->layout->content = $content;
    }

    /**
     * Return a list of layouts belonging to the specified theme
     *
     * @return string
     */
    public function postThemeLayouts()
    {
        $response['status'] = 'OK';

        $theme = Input::get('theme');

        if ($theme != null)
        {
            $layouts = Theme::getThemeLayouts($theme);

            if (!empty($layouts))
            {
                $response['layouts'] = $layouts;
            }
            else
            {
                $response['status'] = 'ERROR';
                $response['message'] = 'No layouts found';
            }
        }
        else
        {
            $response['status'] = 'ERROR';
            $response['message'] = 'No theme was specified';
        }


        return json_encode($response);
    }

}