<h1>Activation</h1>

<?php if ($new_activation): ?>
    <p>
        Congratulations <?php echo $User->first_name . ' ' . $User->last_name ?>! Your account is now <strong>ACTIVE</strong>. To access your account please follow the link below to login.
    </p>
<?php else: ?>
    <p>
       Your account has already been activated. To access your account please follow the link below to login.
    </p>
<?php endif; ?>

<p>
    <a href="<?php echo site_url('users/login'); ?>">User Login</>
</p>
