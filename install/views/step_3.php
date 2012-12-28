<h1 id="configuration">Configuration</h1>

<?php echo validation_errors('<p class="error">', '</p>'); ?>
<?php foreach ($errors as $error): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endforeach; ?>
<?php echo form_open(); ?>
<p>1 . Please enter your database connection details.</p>
<div class="box form">
    <div>
        <label for="hostname">Database Host:<span class="required">*</span></label>
        <?php echo form_input(array('name' => 'hostname', 'id' => 'hostname', 'value' => set_value('hostname', 'localhost'))); ?>
    </div>
    <div>
        <label for="username">Username:<span class="required">*</span></label>
        <?php echo form_input(array('name' => 'username', 'id' => 'username', 'value' => set_value('username'))); ?>
    </div>
    <div>
        <label for="password">Password:<span class="required">*</span></label>
        <?php echo form_password(array('name' => 'password', 'id' => 'password', 'value' => set_value('password'))); ?>
    </div>
    <div>
        <label for="database">Database Name:<span class="required">*</span></label>
        <?php echo form_input(array('name' => 'database', 'id' => 'database', 'value' => set_value('database'))); ?>
    </div>
    <div>
        <label for="port">Database Port:<span class="required">*</span></label>
        <?php echo form_input(array('name' => 'port', 'id' => 'port', 'value' => set_value('port', '3306'))); ?>
    </div>
    <div>
        <label for="prefix">Database Prefix:</label>
        <?php echo form_input(array('name' => 'prefix', 'id' => 'prefix', 'value' => set_value('prefix'))); ?>
    </div>
</div>

<p>2. Please enter a username and password for the administration.</p>

<div class="box form">
    <div>
        <label for="email">Email:<span class="required">*</span></label>
        <?php echo form_input(array('name' => 'email', 'id' => 'email', 'value' => set_value('email'))); ?>
    </div>
    <div>
        <label for="admin_password">Password:<span class="required">*</span></label>
        <?php echo form_password(array('name' => 'admin_password', 'id' => 'admin_password')); ?>
    </div>
    <div>
        <label for="confirm_admin_password">Confirm Password:<span class="required">*</span></label>
        <?php echo form_password(array('name' => 'confirm_admin_password', 'id' => 'confirm_admin_password')); ?>
    </div>
</div>
<div class="align_right">
    <input type="submit" name="submit" value="Continue" />
</div>
<?php echo form_close(); ?>