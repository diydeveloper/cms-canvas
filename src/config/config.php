<?php
 
use CmsCanvas\Models\Setting;

$paths = [
    'public_root' => 'diyphpdeveloper/cmscanvas/',
    'theme_assets' => 'diyphpdeveloper/cmscanvas/themes/',
    'app_themes_directory' => app_path().'/resources/themes/',
    'themes_directory' => __DIR__.'/../resources/themes/',
    'uploads' => 'diyphpdeveloper/cmscanvas/uploads/',
    'thumbnails' => 'diyphpdeveloper/cmscanvas/thumbnails/',
];

if ($this->app->runningInConsole()) {
    return $paths;
}

$settings = Cache::rememberForever('settings', function() {
 
    foreach(Setting::all() as $setting) {
        $settings[$setting->setting] = $setting->value;
    }
 
    return $settings;
});

return array_merge($settings, $paths);