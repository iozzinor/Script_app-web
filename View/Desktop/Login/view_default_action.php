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
        $wrong_attemps_message = sprintf(_dn('login', 'One wrong attempt.', '%d wrong attempts.', $attempts), $attempts);
        print('<p class="login_error">' . $wrong_attemps_message . '</p>');
    }
    
    ?>

    <input type="submit" value="<?= _d('login', 'login_submit_button');?>" />
</form>

<script src="/Content/Scripts/login.js"></script>