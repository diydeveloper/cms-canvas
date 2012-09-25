<h1>Login</h1>

<?php echo validation_errors()?>
<?php echo $this->session->flashdata('message'); ?>


<?php echo form_open(); ?>
    <div>
        <?php echo form_label('Email:', 'email')?>
        <?php echo form_input(array('id' => 'email', 'name' => 'email', 'value' => set_value('email'))); ?>
    </div>

    <div>
        <?php echo form_label('Password:', 'password')?>
        <?php echo form_password(array('id' => 'password', 'name' => 'password', 'value' => set_value('password'))); ?>
    </div>

    <div>
        <?php echo form_label('&nbsp;', '')?>
        <?php echo form_submit('submitForm', 'Login', 'id="login"'); ?>
    </div>

    <div>
        <?php echo form_label('', '')?>
        <?php echo anchor('/users/forgot-password', 'Forgot Password'); ?>
        <?php if ($this->settings->users_module->enable_registration): ?>
            &nbsp;|&nbsp; <?php echo anchor('/users/register', 'Register Now'); ?>
        <?php endif; ?>

    </div>
<?php echo form_close(); ?>
