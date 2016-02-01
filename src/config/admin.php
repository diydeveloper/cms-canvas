<?php

return [
    'url_prefix' => 'sitemin',

    'navigation' => [
        [
            'title' => 'Dashboard',
            'route_name' => 'admin.index',
            'id' => 'dashboard',
            'children' => [],
            'associated_route_names' => [
                'admin.index',
                'admin.dashboard',
            ]
        ],
        [
            'title' => 'Content',
            'route_name' => 'admin.content.entry.entries',
            'children' => [
                [
                    'title' => 'Entries',
                    'route_name' => 'admin.content.entry.entries',
                ],
                [
                    'title' => 'Navigations',
                    'route_name' => 'admin.content.navigation.navigations',
                ],
            ],
            'associated_route_names' => [
                'admin.content.entry.*',
                'admin.content.navigation.*',
            ]
        ],
        [
            'title' => 'Users',
            'route_name' => 'admin.user.users',
            'children' => [
                [
                    'title' => 'Users',
                    'route_name' => 'admin.user.users',
                ],
                [
                    'title' => 'Roles',
                    'route_name' => 'admin.user.role.roles',
                ],
                [
                    'title' => 'Permissions',
                    'route_name' => 'admin.user.permission.permissions',
                ],
            ],
            'associated_route_names' => [
                'admin.user.*',
            ]
        ],
        [
            'title' => 'Tools',
            'route_name' => 'admin.content.type.types',
            'children' => [
                [
                    'title' => 'Content Types',
                    'route_name' => 'admin.content.type.types',
                ],
            ],
            'associated_route_names' => [
                'admin.content.type.*',
            ]
        ],
        [
            'title' => 'System',
            'route_name' => 'admin.system.settings.general-settings',
            'children' => [
                [
                    'title' => 'General Settings',
                    'route_name' => 'admin.system.settings.general-settings',
                ],
                [
                    'title' => 'Languages',
                    'route_name' => 'admin.system.language.languages',
                ],
                [
                    'title' => 'Server Info',
                    'route_name' => 'admin.system.settings.server-info',
                ],
            ],
            'associated_route_names' => [
                'admin.system.*',
            ]
        ],
    ]
];