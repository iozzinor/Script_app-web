(function(Login) {
    var usernameField = document.getElementById('username');
    var passwordField = document.getElementById('password');

    // focus
    if (usernameField.value.length > 0)
    {
        passwordField.focus();
        passwordField.select();
    }
    else
    {
        usernameField.focus();
    }

    Login.validateUserInput = function() {

        var username = usernameField.value;
        var password = passwordField.value;

        if (username === "" || password === "")
        {
            Dialog.appendDialogBox('Login error', 'Please provide a username and a password.');

            return false;
        }

        return true;
    };
})(window.Login = window.Login || {});
