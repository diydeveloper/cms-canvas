<h1>Register</h1>

<?php echo validation_errors()?>

<?php echo form_open(); ?>

    <div>
        <?php echo form_label('<span class="required">*</span> Email:', 'email')?>
        <?php echo form_input(array('id' => 'email', 'name' => 'email', 'value' => set_value('email'))); ?>
    </div>

    <div>
        <?php echo form_label('<span class="required">*</span> Create Password:', 'password')?>
        <?php echo form_password(array('id' => 'password', 'name' => 'password', 'value' => set_value('password'))); ?>
    </div>

    <div>
        <?php echo form_label('<span class="required">*</span> Confirm Password:', 'confirm_password')?>
        <?php echo form_password(array('id' => 'confirm_password', 'name' => 'confirm_password', 'value' => set_value('confirm_password'))); ?>
    </div>

    <div>
        <?php echo form_label('<span class="required">*</span> First Name:', 'first_name')?>
        <?php echo form_input(array('id' => 'first_name', 'name' => 'first_name', 'value' => set_value('first_name'))); ?>
    </div>

    <div>
        <?php echo form_label('<span class="required">*</span> Last Name:', 'last_name')?>
        <?php echo form_input(array('id' => 'last_name', 'name' => 'last_name', 'value' => set_value('last_name'))); ?>
    </div>

    <div>
        <?php echo form_label('<span class="required">*</span> Phone:', 'phone')?>
        <?php echo form_input(array('id' => 'phone', 'name' => 'phone', 'value' => set_value('phone'))); ?>
    </div>

    <div>
        <?php echo form_label('<span class="required">*</span> Address:', 'address')?>
        <?php echo form_input(array('id' => 'address', 'name' => 'address', 'value' => set_value('address'))); ?>
    </div>

    <div>
        <?php echo form_label('Address 2:', 'address2')?>
        <?php echo form_input(array('id' => 'address2', 'name' => 'address2', 'value' => set_value('address2'))); ?>
    </div>

    <div>
        <?php echo form_label('<span class="required">*</span> City:', 'city')?>
        <?php echo form_input(array('id' => 'city', 'name' => 'city', 'value' => set_value('city'))); ?>
    </div>

    <div>
        <?php echo form_label('<span class="required">*</span> State:', 'state')?>
        <?php echo form_dropdown('state', $states, set_value('state')); ?>
    </div>

    <div>
        <?php echo form_label('<span class="required">*</span> Zip:', 'zip')?>
        <?php echo form_input(array('id' => 'zip', 'name' => 'zip', 'value' => set_value('zip'))); ?>
    </div>

    <div class="hide">
        <?php echo form_label('<span class="required">*</span> Spam Check:', 'spam_check')?>
        <?php echo form_input(array('id' => 'spam_check', 'name' => 'spam_check', 'value' => set_value('spam_check'))); ?>
    </div>

    <div>
        <?php echo form_label('&nbsp;', '')?>
        <?php echo form_submit('submitForm', 'Register', 'class="submit"')?>
    </div>

    <div class="clear"></div>

<?php echo form_close(); ?>

<br />

<p>
    Already a Member? <a class="button" href="<?php echo site_url('users/login'); ?>">Login</a>
</p>
