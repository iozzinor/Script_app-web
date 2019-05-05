(function(SignIn) {
    function setupPasswordInformationButton()
    {
        let passwordLabelContainers = document.querySelectorAll('.password_label_container');

        for (var i = 0; i < passwordLabelContainers.length; i++)
        {
            let informationButton = createHoverableButton(undefined,  _d('sign_in', 'Information'), 'blue', 'white');
            informationButton.className = 'information_button';
            passwordLabelContainers[i].appendChild(informationButton);
    
            informationButton.addEventListener('click', function(event) {
                Dialog.appendDialogBox(_d('sign_in', 'Password Policy'), 'This is the information', [informationOkButtonHandler]);
            });
        }
    }

    function setupSignInButton()
    {
        let form = document.getElementById('sign_in_form');

        let signInButton = document.createElement('input');
        signInButton.id = 'sign_in_button';
        createHoverableButton(signInButton, _d('sign_in', 'Create the account'), 'var(--hover-button-default-border)', 'var(--hover-button-default-bg');
        signInButton.type = 'submit';

        form.appendChild(signInButton);
    }

    let informationOkButtonHandler = new Dialog.ButtonHandler(createHoverableButton.bind(null, undefined, 'OK', 'var(--hover-button-default-border)', 'var(--hover-button-default-bg'));
    setupPasswordInformationButton();
    setupSignInButton();

    SignIn.validate = function(event) {
        console.log('sign in fired');
        return false;
    };
})(window.SignIn = window.SignIn || {});