<form id="sign_in_form" method='POST' onsubmit="return SignIn.validate();">
    <div id="account_section" class="sign_in_section">
        <p class="sign_in_section_caption"><?= _d('sign_in', 'Account'); ?></p>

        <div class="sign_in_row">
            <label for="username"><?= _d('sign_in', 'Username:'); ?></label>
            <input id="username" name="username" type="text" placeholder="<?=_d('sign_in', 'The username...') ?>" />
        </div>
        <div class="sign_in_row">
            <div class="password_label_container">
                <label id="password_label" for="password"><?= _d('sign_in', 'Password:'); ?></label>
            </div>
            <input id="password" name="password" type="password" placeholder="<?=_d('sign_in', 'The password...') ?>" />
        </div>
        <div class="sign_in_row">
            <div class="password_label_container">
                <label for="password"><?= _d('sign_in', 'Password Confirmation:'); ?></label>
            </div>
            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="<?=_d('sign_in', 'The password confirmation...') ?>" />
        </div>
        <div class="sign_in_row">
            <label for="mail_address"><?= _d('sign_in', 'Mail Address:'); ?></label>
            <input id="mail_address" name="mail_address" type="text" placeholder="<?=_d('sign_in', 'The mail address...') ?>" />
        </div>
    </div>

    <div id="privileges_section" class="sign_in_section">
        <p class="sign_in_section_caption"><?= _d('sign_in', 'Privileges') ?></p>
        <?php
            if ($no_prefilled_code || $wrong_prefilled_code)
            {
                // display needs privilege checkbox
        ?>
                <input id="needs_privilege_upgrade" type="checkbox" />
                <label for="needs_privilege_upgrade"><?= _d('sign_in', 'I am a teacher or an expert.') ?></label>
                <p id="privileges_status" class="privileges_information"></p>
        <?php

                // display account privileges information
                $privileges_information_format = _d('sign_in', 'Please consult %s for more information.');
                $privileges_information_link = '<a href="' . Router::get_base_url() . 'sign_in/privileges_information">' . _d('sign_in', 'the privilege information page') . '</a>';
                $privileges_information_content = sprintf($privileges_information_format, $privileges_information_link);

                print('<p class="privileges_information">' . $privileges_information_content . '<p>');
            }
            else
            {
        ?>
        <p>test</p>
        <?php
            }
        ?>
    </div>
</form>

<?= $sign_in_types_script ?>
<?php
    // display wrong activation code
    if (isset($wrong_prefilled_code_script))
    {
        print($wrong_prefilled_code_script);
    }
?>