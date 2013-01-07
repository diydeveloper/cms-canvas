<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" /> 
        <title>CMS Canvas Install</title>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('/assets/css/reset.css'); ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('/assets/css/style.css'); ?>" />
    </head>
    <body>
        <div id="container">

            <!-- Header -->
            <header>
                <img alt="Header Image" src="<?php echo base_url('/assets/images/logo.png'); ?>" />
            </header>

            <!-- Main Content -->
            <div id="content_wrapper">
                <div id="left_column">
                    <?php echo $content; ?>
                </div>
                <div id="right_column">
                    <ul>
                        <li <?php echo ($this->uri->segment(1) == 'step1') ? 'class="current"' : ''; ?>><span class="step">1.</span> License</li>
                        <li <?php echo ($this->uri->segment(1) == 'step2') ? 'class="current"' : ''; ?>><span class="step">2.</span> Pre-Installation</li>
                        <li <?php echo ($this->uri->segment(1) == 'step3') ? 'class="current"' : ''; ?>><span class="step">3.</span> Configuration</li>
                        <li <?php echo ($this->uri->segment(1) == 'step4') ? 'class="current"' : ''; ?>><span class="step">4.</span> Finished</li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>

            <footer>
            </footer>

        </div><!-- container -->
    </body>
</html>