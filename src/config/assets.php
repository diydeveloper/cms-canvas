<?php

return array(

    'packages' => array(
        'jquery' => array(
            'javascript' => array(
                Theme::asset('js/jquery-2.1.1.min.js', 'admin'),
                Theme::asset('js/jquery-migrate-1.2.1.min.js', 'admin'),
            ),
        ),
        'jquerytools' => array(
            'javascript' => array(
                Theme::asset('js/jquery.tools.min.js', 'admin'),
            ),
        ),
        'labelify' => array(
            'javascript' => array(
                Theme::asset('js/jquery.labelify.js', 'admin'),
            ),
        ),
        'tablednd' => array(
            'javascript' => array(
                Theme::asset('js/jquery.tablednd_0_7.js', 'admin'),
            ),
        ),
        'superfish' => array(
            'javascript' => array(
                Theme::asset('js/superfish.js', 'admin'),
            ),
        ),
        'zclip' => array(
            'javascript' => array(
                Theme::asset('js/zclip/jquery.zclip.min.js', 'admin'),
            ),
        ),
        'jquerycycle' => array(
            'javascript' => array(
                Theme::asset('js/jquery.cycle.all.min.js', 'admin'),
            ),
        ),
        'tinymce' => array(
            'javascript' => array(
                Theme::asset('js/tiny_mce/tiny_mce.js', 'admin'),
            ),
        ),
        'ckeditor' => array(
            'javascript' => array(
                Theme::asset('js/ckeditor/ckeditor.js', 'admin'),
            ),
        ),
        'ck_jq_adapter' => array(
            'javascript' => array(
                Theme::asset('js/ckeditor/adapters/jquery.js', 'admin'),
            ),
        ),
        'fancybox' => array(
            'javascript' => array(
                Theme::asset('js/fancybox/jquery.fancybox-1.3.4.pack.js', 'admin'),
            ),
            'stylesheet' => array(
                Theme::asset('js/fancybox/jquery.fancybox-1.3.4.css', 'admin'),
            ),
        ),
        'nestedSortable' => array(
            'javascript' => array(
                Theme::asset('js/nested_sortable/jquery.ui.nestedSortable.js', 'admin'),
            ),
            'stylesheet' => array(
                Theme::asset('js/nested_sortable/jquery.ui.nestedSortable.css', 'admin'),
            ),
        ),
        'codemirror' => array(
            'javascript' => array(
                Theme::asset('js/codemirror-4.1/lib/codemirror.js', 'admin'),
                Theme::asset('js/codemirror-4.1/mode/xml/xml.js', 'admin'),
                Theme::asset('js/codemirror-4.1/mode/javascript/javascript.js', 'admin'),
                Theme::asset('js/codemirror-4.1/mode/css/css.js', 'admin'),
                Theme::asset('js/codemirror-4.1/mode/clike/clike.js', 'admin'),
                Theme::asset('js/codemirror-4.1/mode/php/php.js', 'admin'),
            ),
            'stylesheet' => array(
                Theme::asset('js/codemirror-4.1/lib/codemirror.css', 'admin'),
            ),
        ),
        'admin_jqueryui' => array(
            'javascript' => array(
                Theme::asset('js/jqueryui/jquery-ui-1.9.2.custom.min.js', 'admin'),
                Theme::asset('js/jquery-ui-timepicker-addon.js', 'admin'),
            ),
            'stylesheet' => array(
                Theme::asset('js/jqueryui/smoothness/jquery-ui-1.9.2.custom.css', 'admin'),
            ),
        ),
    )

);