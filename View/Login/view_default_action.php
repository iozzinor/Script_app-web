<form action="login" method="POST" onsubmit='return Login.validateUserInput();'>
    <label for='username'>Username:</label>
    <input id="username" name="username" placeholder="The username..." value="<?= $username; ?>" />

    <label for='password'>Password:</label>
    <input id="password" name="password" type="password" placeholder="The password..." value="<?= $password; ?>" />

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

    <input type="submit" value="Login" />
</form>

<script src="Scripts/login.js"></script>