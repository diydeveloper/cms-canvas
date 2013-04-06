<h1 id="installation">Step 2 - Pre-Installation</h1>
<?php echo validation_errors('<p class="error">', '</p>'); ?>
<?php foreach ($errors as $error): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endforeach; ?>
<p>1. Please configure your PHP settings to match requirements listed below.</p>
<div class="box">
    <table width="100%">
        <thead>
            <tr>
                <th class="align_left">PHP Settings</th>
                <th>Current Settings</th>
                <th>Required Settings</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>PHP Version:</td>
                <td class="align_center"><?php echo phpversion(); ?></td>
                <td class="align_center">5.1.6+</td>
                <td class="align_center"><img src="<?php echo base_url() . 'assets/images/' . ((phpversion() >= '5.1.6') ? 'good' : 'bad'); ?>.png" /></td>
            </tr>
            <tr>
                <td>Register Globals:</td>
                <td class="align_center"><?php echo (ini_get('register_globals')) ? 'On' : 'Off'; ?></td>
                <td class="align_center">Off</td>
                <td class="align_center"><img src="<?php echo base_url() . 'assets/images/' . (( ! ini_get('register_globals')) ? 'good' : 'bad'); ?>.png" /></td>
            </tr>
            <tr>
                <td>Magic Quotes GPC:</td>
                <td class="align_center"><?php echo (ini_get('magic_quotes_gpc')) ? 'On' : 'Off'; ?></td>
                <td class="align_center">Off</td>
                <td class="align_center"><img src="<?php echo base_url() . 'assets/images/' . (( ! ini_get('magic_quotes_gpc')) ? 'good' : 'bad'); ?>.png" /></td>
            </tr>
            <tr>
                <td>File Uploads:</td>
                <td class="align_center"><?php echo (ini_get('file_uploads')) ? 'On' : 'Off'; ?></td>
                <td class="align_center">On</td>
                <td class="align_center"><img src="<?php echo base_url() . 'assets/images/' . ((ini_get('file_uploads')) ? 'good' : 'bad'); ?>.png" /></td>
            </tr>
            <tr>
                <td>Session Auto Start:</td>
                <td class="align_center"><?php echo (ini_get('session_auto_start')) ? 'On' : 'Off'; ?></td>
                <td class="align_center">Off</td>
                <td class="align_center"><img src="<?php echo base_url() . 'assets/images/' . (( ! ini_get('session_auto_start')) ? 'good' : 'bad'); ?>.png" /></td>
            </tr>
        </tbody>
    </table>
</div>
<p>2. Please make sure the extensions listed below are installed.</p>
<div class="box">
    <table width="100%">
        <thead>
            <tr>
                <th class="align_left">Extension</th>
                <th>Current Settings</th>
                <th>Required Settings</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>MySQL:</td>
                <td class="align_center"><?php echo extension_loaded('mysql') ? 'On' : 'Off'; ?></td>
                <td class="align_center">On</td>
                <td class="align_center"><img src="<?php echo base_url() . 'assets/images/' . ((extension_loaded('mysql')) ? 'good' : 'bad'); ?>.png" /></td>
            </tr>
            <tr>
                <td>GD:</td>
                <td class="align_center"><?php echo extension_loaded('gd') ? 'On' : 'Off'; ?></td>
                <td class="align_center">On</td>
                <td class="align_center"><img src="<?php echo base_url() . 'assets/images/' . ((extension_loaded('gd')) ? 'good' : 'bad'); ?>.png" /></td>
            </tr>
            <tr>
                <td>cURL:</td>
                <td class="align_center"><?php echo extension_loaded('curl') ? 'On' : 'Off'; ?></td>
                <td class="align_center">On</td>
                <td class="align_center"><img src="<?php echo base_url() . 'assets/images/' . ((extension_loaded('curl')) ? 'good' : 'bad'); ?>.png" /></td>
            </tr>
        </tbody>
    </table>
</div>
<p>3. Please make sure you have set the correct permissions on the files list below.</p>
<div class="box">
    <table width="100%">
        <thead>
            <tr>
                <th class="align_left">Files</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo CMS_ROOT . 'application/config/config.php'; ?></td>
                <td class="align_center"><?php echo is_writable(CMS_ROOT . 'application/config/config.php') ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
            </tr>
            <tr>
                <td><?php echo CMS_ROOT . 'application/config/database.php'; ?></td>
                <td class="align_center"><?php echo is_writable(CMS_ROOT . 'application/config/database.php') ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<p>4. Please make sure you have set the correct permissions on the directories list below.</p>
<div class="box">
    <table width="100%">
        <thead>
            <tr>
                <th class="align_left">Directories</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($writable_dirs as $path => $is_writable): ?>
            <tr>
                <td><?php echo CMS_ROOT . $path; ?></td>
                <td class="align_center"><?php echo $is_writable ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="align_right">
    <?php echo form_open(); ?>
    <input type="submit" name="submit" value="Continue" />
    <?php echo form_close(); ?>
</div>