<!DOCTYPE html>
<html lang="{!! Lang::getLocale() !!}">
    <head>
        <meta charset="UTF-8" /> 

        {!! Theme::head() !!}

        <!-- Stylesheet Includes -->
        <link rel="stylesheet" type="text/css" href="{!! Theme::asset('css/reset.css') !!}" />
        <link rel="stylesheet" type="text/css" href="{!! Theme::asset('css/style.css') !!}" />
        <link rel="stylesheet" type="text/css" href="{!! Theme::asset('css/content.css') !!}" />

        <!-- Javascript Includes -->
        <script type="text/javascript" src="{!! Theme::asset('js/jquery.min.js') !!}"></script>
        <script type="text/javascript" src="{!! Theme::asset('js/superfish.js') !!}"></script>

        <!--[if lt IE 9]>
            <script src="{!! Theme::asset('js/html5.js') !!}"></script>
        <![endif]-->

        <script type="text/javascript">
            $(document).ready(function() {
                $('nav > ul').superfish({
                    hoverClass   : 'sfHover',
                    pathClass    : 'overideThisToUse',
                    delay        : 0,
                    animation    : {height: 'show'},
                    speed        : 'normal',
                    autoArrows   : false,
                    dropShadows  : false, 
                    disableHI    : false, /* set to true to disable hoverIntent detection */
                    onInit       : function(){},
                    onBeforeShow : function(){},
                    onShow       : function(){},
                    onHide       : function(){}
                });
                
                $('nav > ul').css('display', 'block');
            });
        </script>
    </head>


    <body>
        <div id="container">

            <!-- Header -->
            <header>
                <a id="logo" href="{!! url() !!}">{{ Config::get('cmscanvas::config.site_name') }}</a> 

                <img alt="Header Image" src="{!! Theme::asset('images/header.jpg') !!}" />

                <nav>
                    {!! Content::navigation(['navigation_id' => '2']) !!}
                    <div class="clear"></div>
                </nav>
            </header>

            @yield('content')

            <footer>
                <ul>
                    <li>&copy;{{ date('Y') }}  All Rights Reserved</li>
                </ul>
            </footer>

        </div><!-- container -->

        {!! Theme::footer() !!}
        <pre>
            {!! print_r(DB::getQueryLog()) !!}
        </pre>
    </body>

</html>