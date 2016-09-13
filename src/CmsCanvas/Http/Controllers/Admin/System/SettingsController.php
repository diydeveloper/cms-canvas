<?php 

namespace CmsCanvas\Http\Controllers\Admin\System;

use View, stdClass, Theme, Validator;
use CmsCanvas\Http\Controllers\Admin\AdminController;
use CmsCanvas\Models\Setting;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Timezone;
use Illuminate\Http\Request;

class SettingsController extends AdminController {

    /**
     * Display general settings
     *
     * @return View
     */
    public function getGeneralSettings(Request $request)
    {
        $content = View::make('cmscanvas::admin.system.settings.generalSettings');

        $settingItems = Setting::all();
        $settings = new stdClass;
        foreach ($settingItems as $settingItem) {
            $settings->{$settingItem->setting} = $settingItem->value;
        }

        $content->settings = $settings;
        $content->themes = Theme::getThemes();
        $content->layouts = Theme::getThemeLayouts(config('cmscanvas.config.theme'));
        // @todo add paginated entry search
        $content->entries = Entry::orderBy('title', 'asc')->get();
        $content->timezones = Timezone::all();

        $this->layout->breadcrumbs = [$request->path() => 'General Settings'];
        $this->layout->content = $content;
    }

    /**
     * Update setting values
     *
     * @return void
     */
    public function postGeneralSettings(Request $request)
    {
        $rules = [
            'site_name' => 'required',
            'notification_email' => "required|email",
            'site_homepage' => 'required',
            'custom_404' => 'required',
            'theme' => 'required',
            'layout' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('admin.system.settings.general-settings')
                ->withInput()
                ->with('error', $validator->messages()->all());
        }

        $settingItems = Setting::all();

        foreach ($settingItems as $settingItem) {
            $value = $request->input($settingItem->setting);

            if ($value !== null) {
                $settingItem->value = $value;
                $settingItem->save();
            }
        }

        return redirect()->route('admin.system.settings.general-settings')
            ->with('message', "Settings updated successfully.");
    }

    /**
     * Return a list of layouts belonging to the specified theme
     *
     * @return string
     */
    public function postThemeLayouts(Request $request)
    {
        $response['status'] = 'OK';

        $theme = $request->input('theme');

        if ($theme != null) {
            $layouts = Theme::getThemeLayouts($theme);

            if (! empty($layouts)) {
                $response['layouts'] = $layouts;
            } else {
                $response['status'] = 'ERROR';
                $response['message'] = 'No layouts found';
            }
        } else {
            $response['status'] = 'ERROR';
            $response['message'] = 'No theme was specified';
        }

        return json_encode($response);
    }

    /**
     * Show the server info
     *
     * @return View
     */
    public function getServerInfo(Request $request)
    {
        Theme::addStylesheet(Theme::asset('css/server_info.css'));

        ob_start();
        phpinfo();
        $phpInfo = ob_get_contents();
        ob_end_clean();
         
        $phpInfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpInfo);

        $content = View::make('cmscanvas::admin.system.settings.serverInfo');
        $content->phpInfo = $phpInfo;

        $this->layout->breadcrumbs = [$request->path() => 'Server Info'];
        $this->layout->content = $content;
    }

}