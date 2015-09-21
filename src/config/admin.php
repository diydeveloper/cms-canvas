<?php

return [
    'url_prefix' => 'sitemin',

    'navigation' => [
        [
            'title' => 'Dashboard',
            'url'   => '/',
            'id'    => 'dashboard',
            'sub'   => [],
        ],
        [
            'title' => 'Content',
            'url'   => 'content/entry',
            'sub'   => [
                    [
                        'title' => 'Entries',
                        'url'   => 'content/entry',
                    ],
                    [
                        'title' => 'Navigations',
                        'url'   => 'content/navigation',
                    ],
                ],
        ],
        [
            'title' => 'Users',
            'url'   => 'user',
            'sub'   => [
                    [
                        'title' => 'Users',
                        'url'   => 'user',
                    ],
                    [
                        'title' => 'Roles',
                        'url'   => 'user/role',
                    ],
                    [
                        'title' => 'Permissions',
                        'url'   => 'user/permission',
                    ],
                ],
        ],
        [
            'title' => 'Tools',
            'url'   => 'content/type',
            'sub'   => [
                    [
                        'title' => 'Content Types',
                        'url'   => 'content/type',
                    ],
                    [
                        'title'  => 'Content Type Fields',
                        'url'    => 'content/type/field',
                        'hidden' => TRUE, // Used for selected parents for this section
                    ],
                ],
        ],
        [
            'title' => 'System',
            'url'   => 'system/general-settings',
            'sub'   => [
                    [
                        'title' => 'General Settings',
                        'url'   => 'system/general-settings',
                    ],
                    [
                        'title' => 'Languages',
                        'url'   => 'system/language',
                    ],
                    [
                        'title' => 'Server Info',
                        'url'   => 'system/server-info',
                    ],
                ],
        ],
    ]
];