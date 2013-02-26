<?php 
    $nav_array = array(
        array(
            'title' => 'Dashboard',
            'url'   => '/',
            'id'    => 'dashboard',
            'sub'   => array(),
        ),
        array(
            'title' => 'Content',
            'url'   => 'content/entries',
            'sub'   => array(
                    array(
                        'title' => 'Entries',
                        'url'   => 'content/entries',
                    ),
                    array(
                        'title' => 'Navigations',
                        'url'   => 'navigations',
                    ),
                    array(
                        'title' => 'Galleries',
                        'url'   => 'galleries',
                    ),
                ),
        ),
        array(
            'title' => 'Users',
            'url'   => 'users',
            'sub'   => array(
                    array(
                        'title' => 'Users',
                        'url'   => 'users',
                    ),
                    array(
                        'title' => 'User Groups',
                        'url'   => 'users/groups',
                    ),
                ),
        ),
        array(
            'title' => 'Tools',
            'url'   => 'content/types',
            'sub'   => array(
                    array(
                        'title' => 'Content Types',
                        'url'   => 'content/types',
                    ),
                    array(
                        'title'  => 'Content Fields',
                        'url'    => 'content/fields',
                        'hidden' => TRUE, // Used for selected parents for this section
                    ),
                    array(
                        'title' => 'Code Snippets',
                        'url'   => 'content/snippets',
                    ),
                    array(
                        'title' => 'Categories',
                        'url'   => 'content/categories/groups',
                    ),
                    array(
                        'title' => 'Theme Editor',
                        'url'   => 'settings/theme-editor',
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
                        'title' => 'Clear Cache',
                        'url'   => 'settings/clear-cache',
                    ),
                    array(
                        'title' => 'Server Info',
                        'url'   => 'settings/server-info',
                    ),
                ),
        ),
    );

    echo admin_nav($nav_array);
?>
