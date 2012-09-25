<h1>My Account</h1>
<div id="left_column">
    <ul class="sidenav">
        <li><a <?php echo ($this->uri->uri_string() == '/users/account/profile') ? 'class="current"' : ''; ?> href="<?php echo site_url('users/account/profile'); ?>">Profile</a></li>
        <li><a <?php echo ($this->uri->uri_string() == '/users/account/picture') ? 'class="current"' : ''; ?> href="<?php echo site_url('users/account/picture'); ?>">Profile Picture</a></li>
        <li><a <?php echo ($this->uri->uri_string() == '/users/account/change-password') ? 'class="current"' : ''; ?> href="<?php echo site_url('users/account/change-password'); ?>">Change Password</a></li>
    </ul>
</div>
<div id="two_col_right">
    <?php echo $this->session->flashdata('message'); ?>
    <?php echo validation_errors(); ?>

    <?php echo $content; ?>
</div>
