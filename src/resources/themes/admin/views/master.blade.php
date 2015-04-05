<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8" /> 
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        {!! Theme::metadata() !!}

        <!-- CSS FILES -->
        <link rel="stylesheet" type="text/css" href="{!! Theme::asset('css/reset.css') !!}" />
        <link rel="stylesheet" type="text/css" href="{!! Theme::asset('css/style.css') !!}" />

        <!-- Controller Defined Stylesheets -->
        {!! Theme::stylesheets() !!}

        <script type="text/javascript">
            var ADMIN_PATH = '{!! Admin::getUrlPrefix() !!}';
            var ADMIN_URL = '{!! Admin::url() !!}';
            var THEME_URL = '{!! Theme::asset() !!}';
        </script>

        <!-- Controller Defined JS Files -->
        {!! Theme::javascripts() !!} 

        <script type="text/javascript" src="{!! Theme::asset('js/helpers.js') !!}"></script>
        <script type="text/javascript" src="{!! Theme::asset('js/superfish.js') !!}"></script>

        <!-- Google Analytics -->
        {!! Theme::analytics() !!}
    </head>


    <body>
        <div id="container">

            <!-- Header -->
            <div id="header">
                <div class="div1">
                    <div class="div2"><span id="site_name">{{ Config::get('cmscanvas::config.site_name', 'CMS Canvas') }}</span> <span style="vertical-align: middle; display: inline-block; font-size:13px;">| ADMINISTRATION</span></div>
                    @if (Auth::check())
                        <div class="div3">
                            <a id="current_user" href="javascript:void(0);">
                                {{ Auth::user()->getFullName() }}
                                <img src="{!! Auth::user()->portrait(30, 30, true) !!}" style="margin: 0 2px 0 5px; vertical-align: middle;" />
                                <span class="down_arrow"></span>
                                <span id="current_user_box_pointer" class="box_pointer"></span>
                                <span id="current_user_box_pointer_white" class="box_pointer"></span>
                            </a>
                            <div id="current_user_dropdown">
                                <div id="current_user_info">
                                    <div class="fleft" id="current_user_picture">
                                        <img src="{!! Auth::user()->portrait(96, 96, true) !!}" />
                                    </div>
                                    <div class="fleft" id="current_user_detail">
                                        <div id="current_user_name">{!! Auth::user()->getFullName() !!}</div>
                                        <div>{{ Auth::user()->email }}</div>
                                        <a class="blue_button" href="#">View Profile</a>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div id="action_bar">
                                    <a class="button fleft" href="<?php //echo site_url(ADMIN_PATH . '/users/edit/' . $Current_user->id); ?>"><span>Account</span></a>
                                    <a class="button fright" href="<?php //echo site_url('/users/logout'); ?>"><span>Logout</span></a>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @if (Auth::check())
                    <div id="menu">

                        @include('theme::partials.navigation')

                        <ul class="right">
                            <li id="visit_site"><a class="top" onClick="window.name = 'ee_admin'" target="ee_cms" href="{!! url() !!}"><img src="{!! Theme::asset('images/browser_window.png') !!}" alt="" style="vertical-align:middle; margin-right: 3px;" /> Visit Site</a></li>
                        </ul>
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('#menu > ul').superfish({
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
                                
                                $('#menu > ul').css('display', 'block');
                            });
                        </script>
                    </div>
                @endif
            </div>

            @yield('content')

        <!-- ends the container -->
        </div>

        <!-- Footer -->
        <div id="footer">
            Copyright &copy; {{ date('Y') }}&nbsp; v<?php //echo CC_VERSION ?>
        </div>


        <div id="ajax_status">
            <table id="ajax_status_frame">
                <tr>
                    <td>
                        <div id="ajax_status_animation"><img src="{!! Theme::asset('images/ajax-loader.gif') !!}" /></div>
                        <div id="ajax_status_text"></div>
                    </td>
                </tr>
            </table>
        </div>
    </body>

</html>

