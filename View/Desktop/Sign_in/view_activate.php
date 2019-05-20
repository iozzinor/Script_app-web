<?php
    if ($invalid_activation_code)
    {
    ?>
    <p><?= _d('sign_in', 'Sorry, the activation code you provided appears to be invalid :(') ?></p>
    <?php
    }
    else
    {
        $username_format = _d('sign_in', 'Welcome %s! Thank you for signing in. Your account has properly been activated!');
        $username_message = sprintf($username_format, '<strong>' . $username . '</strong>');

        print('<p>' . $username_message . '</p>');
    }
?>