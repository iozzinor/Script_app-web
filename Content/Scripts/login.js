(function(Login) {
    function setupLoginError()
    {
        let loginError = document.querySelector('.login_error');
        return loginError;
    }

    function setupLoginButton()
    {
        let form = document.getElementById('login_form');

        loginButton.addEventListener('click', Login.perform);

        form.appendChild(loginButton);
    }

    function checkCredentials(username, password)
    {
        let request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            switch (this.readyState)
            {
                case XMLHttpRequest.OPENED:
                    loginButton.setDisabled(true);
                    break;
                default:
                    break;
            }
        };

        request.onloadend = function() {
            loginButton.setDisabled(false);

            switch (this.status)
            {
                // error
                case 401:
                    loginError.style.visibility = 'visible';
                    loginError.innerHTML = this.responseText;
                    break;
                // success
                case 200:
                    let newLanguage = this.responseText;
                    let redirectionPath = document.getElementById('forward_redirection').value;
                    window.location.replace('/' + newLanguage + '/' + redirectionPath);
                default:
                    break;
            }
        };

        request.open('POST', '/en/login/perform', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.send('username=' + username + '&password=' + password);
    }

    Login.perform = function(event) {

        event.preventDefault();

        var username = usernameField.value;
        var password = passwordField.value;

        if (username === "" || password === "")
        {
            Dialog.appendDialogBox(_d('login', 'Login error'), _d('login', 'Please provide a username and a password.'));

            return false;
        }
        else
        {
            checkCredentials(username, password);
        }
        return false;
    };

    let loginError = setupLoginError();
    let loginButton = createHoverableButton(undefined, _d('login', 'Login'), 'var(--hover-button-default-border', 'var(--hover-button-default-bg');
    createDisableButton(loginButton, 'var(--disable-button-bg)', 'var(--disable-button-border)', 'var(--disable-button-color)')
    setupLoginButton();

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
})(window.Login = window.Login || {});
