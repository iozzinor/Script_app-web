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
    </div>

    <div id="privileges_section" class="sign_in_section">
        <p class="sign_in_section_caption"><?= _d('sign_in', 'Privileges') ?></p>
        <button id="choose_privileges">Choose Privileges<button>
    </div>
</form>

<?= $sign_in_types_script ?>