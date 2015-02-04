<?php

return array(

    'packages' => array(
        'jquery' => array(
            'javascript' => array(
                Theme::asset('js/jquery-2.1.1.min.js', 'cmscanvas::admin'),
                Theme::asset('js/jquery-migrate-1.2.1.min.js', 'cmscanvas::admin'),
            ),
        ),
        'jquerytools' => array(
            'javascript' => array(
                Theme::asset('js/jquery.tools.min.js', 'cmscanvas::admin'),
            ),
        ),
        'labelify' => array(
            'javascript' => array(
                Theme::asset('js/jquery.labelify.js', 'cmscanvas::admin'),
            ),
        ),
        'tablednd' => array(
            'javascript' => array(
                Theme::asset('js/jquery.tablednd_0_7.js', 'cmscanvas::admin'),
            ),
        ),
        'superfish' => array(
            'javascript' => array(
                Theme::asset('js/superfish.js', 'cmscanvas::admin'),
            ),
        ),
        'zclip' => array(
            'javascript' => array(
                Theme::asset('js/zclip/jquery.zclip.min.js', 'cmscanvas::admin'),
            ),
        ),
        'jquerycycle' => array(
            'javascript' => array(
                Theme::asset('js/jquery.cycle.all.min.js', 'cmscanvas::admin'),
            ),
        ),
        'tinymce' => array(
            'javascript' => array(
                Theme::asset('js/tiny_mce/tiny_mce.js', 'cmscanvas::admin'),
            ),
        ),
        'ckeditor' => array(
            'javascript' => array(
                Theme::asset('js/ckeditor/ckeditor.js', 'cmscanvas::admin'),
            ),
        ),
        'ck_jq_adapter' => array(
            'javascript' => array(
                Theme::asset('js/ckeditor/adapters/jquery.js', 'cmscanvas::admin'),
            ),
        ),
        'fancybox' => array(
            'javascript' => array(
                Theme::asset('js/fancybox/jquery.fancybox-1.3.4.pack.js', 'cmscanvas::admin'),
            ),
            'stylesheet' => array(
                Theme::asset('js/fancybox/jquery.fancybox-1.3.4.css', 'cmscanvas::admin'),
            ),
        ),
        'nestedSortable' => array(
            'javascript' => array(
                Theme::asset('js/nested_sortable/jquery.ui.nestedSortable.js', 'cmscanvas::admin'),
            ),
            'stylesheet' => array(
                Theme::asset('js/nested_sortable/jquery.ui.nestedSortable.css', 'cmscanvas::admin'),
            ),
        ),
        'codemirror' => array(
            'javascript' => array(
                Theme::asset('js/codemirror-4.1/lib/codemirror.js', 'cmscanvas::admin'),
                Theme::asset('js/codemirror-4.1/mode/xml/xml.js', 'cmscanvas::admin'),
                Theme::asset('js/codemirror-4.1/mode/javascript/javascript.js', 'cmscanvas::admin'),
                Theme::asset('js/codemirror-4.1/mode/css/css.js', 'cmscanvas::admin'),
                Theme::asset('js/codemirror-4.1/mode/clike/clike.js', 'cmscanvas::admin'),
                Theme::asset('js/codemirror-4.1/mode/php/php.js', 'cmscanvas::admin'),
            ),
            'stylesheet' => array(
                Theme::asset('js/codemirror-4.1/lib/codemirror.css', 'cmscanvas::admin'),
            ),
        ),
        'admin_jqueryui' => array(
            'javascript' => array(
                Theme::asset('js/jqueryui/jquery-ui-1.9.2.custom.min.js', 'cmscanvas::admin'),
                Theme::asset('js/jquery-ui-timepicker-addon.js', 'cmscanvas::admin'),
            ),
            'stylesheet' => array(
                Theme::asset('js/jqueryui/smoothness/jquery-ui-1.9.2.custom.css', 'cmscanvas::admin'),
            ),
        ),
        'image_field' => array(
            'javascript' => array(
                Theme::asset('js/image_field.js', 'cmscanvas::admin'),
            )
        ),
    )

);