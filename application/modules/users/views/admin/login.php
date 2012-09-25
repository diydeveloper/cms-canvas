<div class="box" style="width: 400px; min-height: 300px; margin-top: 40px; margin-left: auto; margin-right: auto;">
    <div class="heading">
        <h1><img src="<?php echo theme_url('assets/images/lockscreen.png'); ?>" alt="" /> Please enter your login details.</h1>
    </div>
    <div id="login_form" class="content">
        <?php echo form_open(); ?>
            <?php echo validation_errors(); ?>
            <?php echo $this->session->flashdata('message'); ?>

            <div class="image">
                <img src="<?php echo theme_url('assets/images/login.png'); ?>" alt="Please enter your login details." />
                <br />
                <?php echo anchor(ADMIN_PATH . '/users/forgot-password', 'Forgot Password') ?>
            </div>

            <div class="fields">
                <div>
                    <?php echo form_label('Email', 'email'); ?>
                    <?php echo form_input(array('id' => 'email', 'name' => 'email', 'value' => set_value('email'))); ?>
                </div>

                <div>
                    <?php echo form_label('Password', 'password'); ?>
                    <?php echo form_password(array('id' => 'password', 'name' => 'password')); ?>
                </div>

                <div>
                    <div class="fleft">
                        <label><input name="remember_me" class="remember_me" type="checkbox" value="1" /> Remember Me</label>
                    </div>
                    <div class="fright">
                        <button class="button" type="submit"><span>Login</span></button>
                    </div>
                    <div class="clear"></div>
                </div>
                

            </div>

        <?php echo form_close(); ?>
    </div>
</div>
