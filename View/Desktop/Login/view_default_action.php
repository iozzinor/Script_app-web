<div class="login">
    <form id="login_form" method="POST" onsubmit="return Login.perform(event);">
        <table>
            <tr>
                <td><label for='username'><?= _d('login', 'username_label'); ?></label></td>
                <td><input id="username" name="username" placeholder="<?= _d('login', 'username_placeholder'); ?>" value="<?= $username; ?>" /></td>
            </tr>

            <tr>
                <td><label for='password'><?= _d('login', 'password_label'); ?></label></td>
                <td><input id="password" name="password" type="password" placeholder="<?= _d('login', 'password_placeholder'); ?>" value="<?= $password; ?>" /></td>
            </tr>
        </table>

        <input id="forward_redirection" type="hidden" value="<?= $forward_redirection ?>" />
        <?php
            if ($attempts < 1)
            {
                ?>
               <p class="login_error" style="visibility: hidden;"></p>
               <?php
            }
            else
            {
                ?>
               <p class="login_error"><?= $wrong_attemps_message = sprintf(_dn('login', 'One wrong attempt.', '%d wrong attempts.', $attempts), $attempts); ?></p>
               <?php
            }
        ?>
    </form>
    <?php
        $signin_link = '<a href="' . Router::get_base_url() . 'sign_in">' . _d('login', 'signin') . '</a>';
        print('<p>');
        printf(_d('login', 'No account ? %s!'), $signin_link);
        print('</p>');
    ?>
</div>