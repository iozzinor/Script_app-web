(function(SignIn) {
    // -------------------------------------------------------------------------
    // PASSWORD INFORMATION
    // -------------------------------------------------------------------------
    SignIn.usernameMinLength = 2;
    SignIn.usernameMaxLength = 128;
    SignIn.passwordMinLength = 8;
    SignIn.passwordMaxLength = 2048;

    function setupPasswordInformationButton()
    {
        let passwordLabelContainers = document.querySelectorAll('.password_label_container');

        for (var i = 0; i < passwordLabelContainers.length; i++)
        {
            let informationButton = createHoverableButton(undefined,  _d('sign_in', 'Information'), 'var(--information-button-border)', 'var(--information-button-bg)');
            informationButton.className = 'information_button';
            passwordLabelContainers[i].appendChild(informationButton);

            let passwordInformationView = SignIn.createPasswordInformationView();
    
            informationButton.addEventListener('click', function(event) {
                Dialog.appendDialogBox(_d('sign_in', 'Password Policy'), '', [informationOkButtonHandler], passwordInformationView);
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

        createDisableButton(signInButton, 'var(--disable-button-bg)', 'var(--disable-button-border)', 'var(--disable-button-color)');

        form.appendChild(signInButton);

        return signInButton;
    }

    function setupChoosePrivilegesButton()
    {
        let privilegesButton = document.getElementById('choose_privileges');
        createHoverableButton(privilegesButton, '', 'var(--hover-button-default-border)', 'var(--hover-button-default-bg)');
    }

    // -------------------------------------------------------------------------
    // SECTION ERROR
    // -------------------------------------------------------------------------
    function setupErrorSections()
    {
        let errorSections = [];
        let signInSections = document.querySelectorAll('.sign_in_section');

        for (var i = 0; i < signInSections.length; ++i)
        {
            var signInSection = signInSections[i];

            let newErrorSection = document.createElement('div');
            newErrorSection.style.display = 'none';
            newErrorSection.className = 'error_section';

            let newErrorLabel = document.createElement('p');
            newErrorLabel.className = 'error_status';
            newErrorSection.appendChild(newErrorLabel);

            signInSection.appendChild(newErrorSection);
            errorSections.push(newErrorSection);
        }

        return errorSections;
    }

    function clearErrors()
    {
        clearErrorInputs();
        clearErrorSections();
    }

    function clearErrorInputs()
    {
        let errorInputs = document.querySelectorAll('.error_input');
        for (var i = 0; i < errorInputs.length; ++i)
        {
            errorInputs[i].className = '';
        }
    }

    function clearErrorSections()
    {
       for (var i = 0; i < errorSections.length; ++i)
       {
           errorSections[i].style.display = 'none';
       }
    }

    class SectionError
    {
        constructor(sectionNode)
        {
            this.sectionNode    = sectionNode;
            this.errorMessage   = '';
            this.errorDetected  = false;
            this.firstField     = null;
        }

        addError(message, field)
        {
            this.errorDetected = true;
            if (this.errorMessage !== '')
            {
                this.errorMessage += '<br />';
            }
            this.errorMessage += message;

            if (field != null)
            {
                field.className = 'error_input';
                this.firstField = this.firstField || field;
            }
        }

        display()
        {
            let errorSection = this.sectionNode.querySelector('.error_section');
            let errorLabel = errorSection.querySelector('.error_status');
            errorLabel.innerHTML = this.errorMessage;
            errorSection.style.display = 'block';
        }

        hasDetectedError()
        {
            return this.errorDetected;
        }
    }

    // -------------------------------------------------------------------------
    // VALIDATION
    // -------------------------------------------------------------------------
    SignIn.validate = function(event) {
        clearErrors();

        let canAttemptSignIn = false;
        try
        {
            canAttemptSignIn = checkForm();
        }
        catch (sectionErrors)
        {
            for (var i = 0; i < sectionErrors.length; ++i)
            {
                sectionErrors[i].display();
            }
        }

        if (canAttemptSignIn)
        {
            attemptSignIn();
        }

        return false;
    };

    function checkForm()
    {
        // check section function must return a SectionError object
        let checkSectionFunctions = [
            checkAccountError,
            checkPrivilegesError
        ];

        var sectionErrors = [];
        for (var i = 0; i < checkSectionFunctions.length; ++i)
        {
            let currentSectionError = checkSectionFunctions[i]();
            if (currentSectionError.hasDetectedError())
            {
                sectionErrors.push(currentSectionError);
            }
        }

        if (sectionErrors.length > 0)
        {
            throw sectionErrors;
        }
        return true;
    }

    // return whether the account form is error free
    function checkAccountError()
    {  
        // get elements
        let accountSection              = document.getElementById('account_section');
        let usernameField               = document.getElementById('username');
        let passwordField               = document.getElementById('password');
        let passwordConfirmationField   = document.getElementById('password_confirmation');

        var accountError = new SectionError(accountSection);

        let username                    = usernameField.value;
        let password                    = passwordField.value;
        let passwordConfirmation        = passwordConfirmationField.value;

        // username
        if (username.length == 0)
        {
            accountError.addError(_d('sign_in', 'The username is empty.'), usernameField);
        }
        else if (username.length < SignIn.usernameMinLength || username.length > SignIn.usernameMaxLength)
        {
            let usernameLengthFormat = _d('sign_in', 'The username must contain between %1 and %2 characters.');
            accountError.addError(Main.sprintf(usernameLengthFormat, [SignIn.usernameMinLength, SignIn.usernameMaxLength]), usernameField);
        }

        // password should not be empty
        if (password.length == 0)
        {
            accountError.addError(_d('sign_in', 'The password is empty.'), passwordField);
        }
        else
        {
            // password length
            if(password.length < SignIn.passwordMinLength || password.length > SignIn.passwordMaxLength)
            {
                let passwordLengthFormat = _d('sign_in', 'The password must contain between %1 and %2 characters.');
                accountError.addError(Main.sprintf(passwordLengthFormat, [SignIn.passwordMinLength, SignIn.passwordMaxLength]), passwordField);
            }

            // password lowercase
            if (!password.match(/[a-z]/))
            {
                accountError.addError(_d('sign_in', 'The password must contain at least one lowercase character.'), passwordField);
            }

            // password uppercase
            if (!password.match(/[A-Z]/))
            {
                accountError.addError(_d('sign_in', 'The password must contain at least one uppercase character.'), passwordField);
            }

            // password digit
            if (!password.match(/[0-9]/))
            {
                accountError.addError(_d('sign_in', 'The password must contain at least one digit.'), passwordField);
            }
        }

        // password confirmation
        if (passwordConfirmation != password)
        {
            accountError.addError(_d('sign_in', 'The password confirmation does not match the password.'), passwordConfirmationField);
        }

        return accountError;
    }

    function checkPrivilegesError()
    {
        let privilegesSection = document.getElementById('privileges_section');
        let privilegesError = new SectionError(privilegesSection);
        if (Math.random() < 0.5)
        {
            //privilegesError.addError('An error: ' + (new Date()).getTime());
        }

        return privilegesError;
    }

    // -------------------------------------------------------------------------
    // ATTEMPT SIGN IN
    // -------------------------------------------------------------------------
    function createFormData()
    {
        let form = document.getElementById('sign_in_form');
        let formData = new FormData(form);

        return formData;
    }

    function attemptSignIn()
    {
        let request = new XMLHttpRequest();

        request.onloadend = function () {
            signInButton.setDisabled(false);

            switch (this.status)
            {
                case 400:
                    errorResponse = JSON.parse(this.responseText);
                    signInError(errorResponse);
                    break;
                case 200:
                    let newLocation = '/' + Main.getCurrentLanguage() + '/sign_in/success';
                    window.location.replace(newLocation);
                    break;
                default:
                    break;
            }
        };
        request.onreadystatechange = function() {
            switch (this.readyState)
            {
                case XMLHttpRequest.OPENED:
                    signInButton.setDisabled(true);
                    break;
                default:
                    break;
            }
        };

        let formData = createFormData();

        let requestUrl = '/' + Main.getCurrentLanguage() + '/sign_in/perform';
        request.open('POST', requestUrl, true);
        request.send(formData);
    }

    function signInError(responseError)
    {
        let errorElement = document.getElementById(responseError.domain);
        if (errorElement != null)
        {
            // find the section
            let signInSection = Main.findParentMatching(errorElement, element => element.className == 'sign_in_section');

            if (signInSection != null)
            {
                let newSectionError = new SectionError(signInSection);
                newSectionError.addError(responseError.error, errorElement);
                newSectionError.display();
            }
        }
    }

    // -------------------------------------------------------------------------
    // MAIN
    // -------------------------------------------------------------------------
    let informationOkButtonHandler = new Dialog.ButtonHandler(createHoverableButton.bind(null, undefined, _('OK'), 'var(--hover-button-default-border)', 'var(--hover-button-default-bg'));
    setupPasswordInformationButton();
    let signInButton = setupSignInButton();
    setupChoosePrivilegesButton();
    let errorSections = setupErrorSections();
})(window.SignIn = window.SignIn || {});