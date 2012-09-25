<h1>Registration Complete</h1>

<p>
    Your registration was completed successfully.
</p>

<?php if($this->settings->users_module->email_activation): ?>
    <p>
        An email has been sent to your address containing a link to activate your account.
    </p>
<?php endif; ?>
