<?php

return [

    'packages' => [
        'jquery' => [
            'javascript' => [
                Theme::asset('js/jquery-2.1.1.min.js', 'admin'),
                Theme::asset('js/jquery-migrate-1.2.1.min.js', 'admin'),
            ],
        ],
        'jquerytools' => [
            'javascript' => [
                Theme::asset('js/jquery.tools.min.js', 'admin'),
            ],
        ],
        'labelify' => [
            'javascript' => [
                Theme::asset('js/jquery.labelify.js', 'admin'),
            ],
        ],
        'tablednd' => [
            'javascript' => [
                Theme::asset('js/jquery.tablednd_0_7.js', 'admin'),
            ],
        ],
        'superfish' => [
            'javascript' => [
                Theme::asset('js/superfish.js', 'admin'),
            ],
        ],
        'zclip' => [
            'javascript' => [
                Theme::asset('js/zclip/jquery.zclip.min.js', 'admin'),
            ],
        ],
        'jquerycycle' => [
            'javascript' => [
                Theme::asset('js/jquery.cycle.all.min.js', 'admin'),
            ],
        ],
        'tinymce' => [
            'javascript' => [
                Theme::asset('js/tiny_mce/tiny_mce.js', 'admin'),
            ],
        ],
        'ckeditor' => [
            'javascript' => [
                Theme::asset('js/ckeditor/ckeditor.js', 'admin'),
            ],
        ],
        'ck_jq_adapter' => [
            'javascript' => [
                Theme::asset('js/ckeditor/adapters/jquery.js', 'admin'),
            ],
        ],
        'fancybox' => [
            'javascript' => [
                Theme::asset('js/fancybox/jquery.fancybox-1.3.4.pack.js', 'admin'),
            ],
            'stylesheet' => [
                Theme::asset('js/fancybox/jquery.fancybox-1.3.4.css', 'admin'),
            ],
        ],
        'nestedSortable' => [
            'javascript' => [
                Theme::asset('js/nested_sortable/jquery.ui.nestedSortable.js', 'admin'),
            ],
            'stylesheet' => [
                Theme::asset('js/nested_sortable/jquery.ui.nestedSortable.css', 'admin'),
            ],
        ],
        'codemirror' => [
            'javascript' => [
                Theme::asset('js/codemirror-4.1/lib/codemirror.js', 'admin'),
                Theme::asset('js/codemirror-4.1/mode/xml/xml.js', 'admin'),
                Theme::asset('js/codemirror-4.1/mode/javascript/javascript.js', 'admin'),
                Theme::asset('js/codemirror-4.1/mode/css/css.js', 'admin'),
                Theme::asset('js/codemirror-4.1/mode/clike/clike.js', 'admin'),
                Theme::asset('js/codemirror-4.1/mode/php/php.js', 'admin'),
            ],
            'stylesheet' => [
                Theme::asset('js/codemirror-4.1/lib/codemirror.css', 'admin'),
            ],
        ],
        'admin_jqueryui' => [
            'javascript' => [
                Theme::asset('js/jqueryui/jquery-ui-1.9.2.custom.min.js', 'admin'),
                Theme::asset('js/jquery-ui-timepicker-addon.js', 'admin'),
            ],
            'stylesheet' => [
                Theme::asset('js/jqueryui/smoothness/jquery-ui-1.9.2.custom.css', 'admin'),
            ],
        ],
        'image_field' => [
            'javascript' => [
                Theme::asset('js/image_field.js', 'admin'),
            ]
        ],
        'avatar_image_field' => [
            'javascript' => [
                Theme::asset('js/avatar_image_field.js', 'admin'),
            ]
        ],
    ]

];