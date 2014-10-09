<?php
 
use CmsCanvas\Models\Setting;

$paths = array(
    'publicRoot' => 'packages/diyphpdeveloper/cmscanvas/',
    'themeAssets' => 'packages/diyphpdeveloper/cmscanvas/themes/',
    'uploads' => 'packages/diyphpdeveloper/cmscanvas/uploads/',
    'thumbnails' => 'packages/diyphpdeveloper/cmscanvas/thumbnails/',
);

$settings = Cache::rememberForever('settings', function() {
 
    foreach(Setting::all() as $setting)
    {
        $settings[$setting->setting] = $setting->value;
    }
 
    return $settings;
});

return array_merge($settings, $paths);