<?php
 
use CmsCanvas\Models\Setting;

$paths = array(
    'public_root' => 'packages/diyphpdeveloper/cmscanvas/',
    'theme_assets' => 'packages/diyphpdeveloper/cmscanvas/themes/',
    'app_themes_directory' => app_path().'/resources/themes/',
    'themes_directory' => __DIR__.'/../resources/themes/',
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