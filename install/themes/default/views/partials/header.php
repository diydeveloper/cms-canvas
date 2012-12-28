<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" /> 

        {{ template:head }}

        <!-- Stylesheet Includes -->
        <link rel="stylesheet" type="text/css" href="{{ theme_url }}/assets/css/reset.css" />
        <link rel="stylesheet" type="text/css" href="{{ theme_url }}/assets/css/style.css" />
        <link rel="stylesheet" type="text/css" href="{{ theme_url }}/assets/css/content.css" />

        <!-- Javascript Includes -->
        <script type="text/javascript" src="{{ theme_url }}/assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="{{ theme_url }}/assets/js/superfish.js"></script>

        <!--[if lt IE 9]>
            <script src="{{ theme_url }}/assets/js/html5.js"></script>
        <![endif]-->

        <script type="text/javascript">
            $(document).ready(function() {
                $('nav > ul').superfish({
                    hoverClass	 : 'sfHover',
                    pathClass	 : 'overideThisToUse',
                    delay		 : 0,
                    animation	 : {height: 'show'},
                    speed		 : 'normal',
                    autoArrows   : false,
                    dropShadows  : false, 
                    disableHI	 : false, /* set to true to disable hoverIntent detection */
                    onInit		 : function(){},
                    onBeforeShow : function(){},
                    onShow		 : function(){},
                    onHide		 : function(){}
                });
                
                $('nav > ul').css('display', 'block');
            });
        </script>
    </head>


    <body>
        <div id="container">

            <!-- Header -->
            <header>
                <a id="logo" href="{{ site_url }}">{{ settings:site_name }}</a> 

                <img alt="Header Image" src="{{ theme_url }}/assets/images/header.jpg" />

                <nav>
                    {{ navigations:nav nav_id="1" class="left" }}
                    <div class="clear"></div>
                </nav>
            </header>
