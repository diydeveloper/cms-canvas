<h1>Forgot Password</h1>

<?php echo validation_errors()?>

<?php echo  form_open(); ?>

    <div>
        <?php echo  form_label('Enter your email:', 'email'); ?>
        <?php echo  form_input(array('id'=>'email', 'name'=>'email', 'value'=>set_value('email'))); ?>
    </div>

    <div>
        <?php echo form_label('&nbsp;', '')?>
        <?php echo form_submit('submit', 'Submit', 'class="submit"'); ?>
    </div>

<?php echo form_close(); ?>
