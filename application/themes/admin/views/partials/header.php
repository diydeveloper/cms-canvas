<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8" /> 
        <?php echo $this->template->metadata() ?>

        <!-- CSS FILES -->
        <link rel="stylesheet" type="text/css" href="<?php echo theme_url('assets/css/reset.css');  ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo theme_url('assets/css/style.css');  ?>" />

        <!-- Controller Defined Stylesheets -->
        <?php echo $this->template->stylesheets() ?>

        <script type="text/javascript">
            var ADMIN_PATH = '<?php echo ADMIN_PATH; ?>';
            var ADMIN_URL = '<?php echo site_url(ADMIN_PATH); ?>';
            var THEME_URL = '<?php echo theme_url(); ?>';
        </script>

        <!-- Controller Defined JS Files -->
        <?php echo $this->template->javascripts() ?>

        <script type="text/javascript" src="<?php echo theme_url('assets/js/helpers.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo theme_url('assets/js/superfish.js'); ?>"></script>

        <!-- Google Analytics -->
        <?php echo $this->template->analytics() ?>
    </head>


    <body>
        <div id="container">

            <!-- Header -->
            <div id="header">
                <div class="div1">
                    <div class="div2"><span id="site_name"><?php echo $this->settings->site_name ?></span> | ADMINISTRATION</div>
                    <?php if ($this->secure->is_auth()): ?>
                        <div class="div3"><img src="<?php echo theme_url('assets/images/lock.png'); ?>" alt="" style="position: relative; top: 3px;" />&nbsp;You are logged in as <?php echo $this->secure->get_user_session()->first_name . ' ' . $this->secure->get_user_session()->last_name ; ?></div>
                    <?php endif; ?>
                </div>
                <?php if ($this->secure->is_auth()): ?>
                    <div id="menu">

                        <?php echo theme_partial('navigation'); ?>

                        <ul class="right">
                            <li id="store"><a class="top" onClick="window.name = 'ee_admin'" target="ee_cms" href="<?php echo site_url(); ?>">Visit Site</a></li>
                            <li id="store"><a class="top" href="<?php echo site_url('users/logout'); ?>">Logout</a></li>
                        </ul>
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('#menu > ul').superfish({
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
                                
                                $('#menu > ul').css('display', 'block');
                            });
                        </script>
                    </div>
                <?php endif; ?>
            </div>
