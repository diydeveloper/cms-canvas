<?php
 
return [
    /*
    |--------------------------------------------------------------------------
    | Themes Directory
    |--------------------------------------------------------------------------
    |
    | Filesystem path used to store CMS Canvas themes
    |
    */
    'themes_directory' => base_path('/resources/themes/'),

    /*
    |--------------------------------------------------------------------------
    | Theme Assets URL
    |--------------------------------------------------------------------------
    |
    | Defines URL to access assets used in themes. Relative paths will 
    | generate the URL using the asset() helper
    |
    */
    'theme_assets_url' => 'diyphpdeveloper/cmscanvas/themes/',

    /*
    |--------------------------------------------------------------------------
    | Avatars
    |--------------------------------------------------------------------------
    |
    | Filesystem path used to store uploaded avatar images
    |
    */
    'avatars' => public_path('diyphpdeveloper/cmscanvas/uploads/avatars/'),

    /*
    |--------------------------------------------------------------------------
    | Avatars URL
    |--------------------------------------------------------------------------
    |
    | Defines URL to access uploaded avatar images. Relative paths will 
    | generate the URL using the asset() helper
    |
    */
    'avatars_url' => 'diyphpdeveloper/cmscanvas/uploads/avatars/',

    /*
    |--------------------------------------------------------------------------
    | Thumbnails
    |--------------------------------------------------------------------------
    |
    | Filesystem path used to store image thumbnails
    |
    */
    'thumbnails' => public_path('diyphpdeveloper/cmscanvas/thumbnails/'),

    /*
    |--------------------------------------------------------------------------
    | Thumbnails URL
    |--------------------------------------------------------------------------
    |
    | Defines URL to access image thumbnails. Relative paths will 
    | generate the URL using the asset() helper
    |
    */
    'thumbnails_url' => 'diyphpdeveloper/cmscanvas/thumbnails/',
];