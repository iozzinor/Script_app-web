<form action="login" method="POST" onsubmit='return Login.validateUserInput();'>
    <label for='username'><?= _d('login', 'username_label'); ?></label>
    <input id="username" name="username" placeholder="<?= _d('login', 'username_placeholder'); ?>" value="<?= $username; ?>" />

    <label for='password'><?= _d('login', 'password_label'); ?></label>
    <input id="password" name="password" type="password" placeholder="<?= _d('login', 'password_placeholder'); ?>" value="<?= $password; ?>" />

    <?php
    
    if (isset($error))
    {
        print('<p class = "login_error">' . $error . '</p>');
    }
    if ($attempts > 0)
    {
        print('<p class="login_error">' . $attempts . ' attempts</p>');
    }
    
    ?>

    <input type="submit" value="<?= _d('login', 'login_submit_button');?>" />
</form>

<script src="/Content/Scripts/login.js"></script>