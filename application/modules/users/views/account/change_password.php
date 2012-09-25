<?php
$password = array(
                'id'   => 'password',
                'name' => 'password',
                'value' => set_value('password'),
            );

$confirm_password = array(
                'id'   => 'confirm_password',
                'name' => 'confirm_password',
            );
?>
<h2>Change Password</h2>
<br />

<?php echo form_open(); ?>

<div>
    <div>
        <?php echo form_label('New Password:', 'password'); ?>
        <?php echo form_password($password); ?>
    </div>

    <div>
        <?php echo form_label('Confirm Password:', 'confirm_password'); ?>
        <?php echo form_password($confirm_password); ?>
    </div>

    <div>
        <?php echo form_label('&nbsp;', ''); ?>
        <input class="submit" type="submit" value="Change" />
    </div>
</div>

<?php echo form_close(); ?>
