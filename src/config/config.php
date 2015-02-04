<?php
 
use CmsCanvas\Models\Setting;

$paths = array(
    'public_root' => 'packages/diyphpdeveloper/cmscanvas/',
    'theme_assets' => app_path().'/themes/',
    'themes_directory' => app_path().'/themes/',
    'cmscanvas_theme_assets' => 'packages/diyphpdeveloper/cmscanvas/themes/',
    'cmscanvas_themes_directory' => __DIR__.'/../themes/',
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