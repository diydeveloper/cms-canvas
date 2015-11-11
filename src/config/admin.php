<?php

return [
    'url_prefix' => 'sitemin',

    'navigation' => [
        [
            'title' => 'Dashboard',
            'route' => '/',
            'id' => 'dashboard',
            'children' => [],
            'associated_route_names' => [
                'admin.index',
                'admin.dashboard',
            ]
        ],
        [
            'title' => 'Content',
            'route' => 'content/entry',
            'children' => [
                [
                    'title' => 'Entries',
                    'route'   => 'content/entry',
                ],
                [
                    'title' => 'Navigations',
                    'route' => 'content/navigation',
                ],
            ],
            'associated_route_names' => [
                'admin.content.entry.*',
                'admin.content.navigation.*',
            ]
        ],
        [
            'title' => 'Users',
            'route' => 'user',
            'children' => [
                [
                    'title' => 'Users',
                    'route'   => 'user',
                ],
                [
                    'title' => 'Roles',
                    'route'   => 'user/role',
                ],
                [
                    'title' => 'Permissions',
                    'route'   => 'user/permission',
                ],
            ],
            'associated_route_names' => [
                'admin.user.*',
            ]
        ],
        [
            'title' => 'Tools',
            'route' => 'content/type',
            'children' => [
                [
                    'title' => 'Content Types',
                    'route' => 'content/type',
                ],
            ],
            'associated_route_names' => [
                'admin.content.type.*',
            ]
        ],
        [
            'title' => 'System',
            'route' => 'system/general-settings',
            'children' => [
                [
                    'title' => 'General Settings',
                    'route' => 'system/general-settings',
                ],
                [
                    'title' => 'Languages',
                    'route' => 'system/language',
                ],
                [
                    'title' => 'Server Info',
                    'route' => 'system/server-info',
                ],
            ],
            'associated_route_names' => [
                'admin.system.*',
            ]
        ],
    ]
];