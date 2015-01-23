<?php

return array(
    'url_prefix' => 'sitemin',

    'navigation' => array(
        array(
            'title' => 'Dashboard',
            'url'   => '/',
            'id'    => 'dashboard',
            'sub'   => array(),
        ),
        array(
            'title' => 'Content',
            'url'   => 'content/entry',
            'sub'   => array(
                    array(
                        'title' => 'Entries',
                        'url'   => 'content/entry',
                    ),
                    array(
                        'title' => 'Navigations',
                        'url'   => 'content/navigation',
                    ),
                ),
        ),
        array(
            'title' => 'Users',
            'url'   => 'user',
            'sub'   => array(
                    array(
                        'title' => 'Users',
                        'url'   => 'user',
                    ),
                    array(
                        'title' => 'Roles',
                        'url'   => 'user/role',
                    ),
                ),
        ),
        array(
            'title' => 'Tools',
            'url'   => 'content/type',
            'sub'   => array(
                    array(
                        'title' => 'Content Types',
                        'url'   => 'content/type',
                    ),
                    array(
                        'title'  => 'Content Type Fields',
                        'url'    => 'content/type/field',
                        'hidden' => TRUE, // Used for selected parents for this section
                    ),
                ),
        ),
        array(
            'title' => 'System',
            'url'   => 'settings/general-settings',
            'sub'   => array(
                    array(
                        'title' => 'General Settings',
                        'url'   => 'settings/general-settings',
                    ),
                    array(
                        'title' => 'Languages',
                        'url'   => 'system/languages',
                    ),
                    array(
                        'title' => 'Server Info',
                        'url'   => 'settings/server-info',
                    ),
                ),
        ),
    )
);