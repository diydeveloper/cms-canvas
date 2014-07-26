<?php
 
use CmsCanvas\Models\Setting;

return Cache::rememberForever('settings', function() {
 
    $settings = array();
 
    foreach(Setting::all() as $setting)
    {
        $settings[$setting->setting] = $setting->value;
    }
 
    return $settings;
});
