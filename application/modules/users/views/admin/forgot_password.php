<div class="breadcrumb"> </div>
<div class="box">
    <div class="heading">
        <h1><img src="<?php echo theme_url('assets/images/user.png'); ?>" alt="" /> Forgot Your Password?</h1>
        <div class="buttons">
            <a onclick="document.getElementById('forgotten').submit();" class="button"><span>Reset</span></a>
            <a href="<?php echo site_url(ADMIN_PATH . '/users/login'); ?>" class="button"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">
        <?php echo form_open(null, 'id="forgotten"'); ?>
            <div class="form">
                <p>Enter the e-mail address associated with your account. Click submit to have a password reset link e-mailed to you.</p>
                <div>
                    <label for="email">E-Mail Address:</label>
                    <input id="email" type="text" name="email" value="" />
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>
