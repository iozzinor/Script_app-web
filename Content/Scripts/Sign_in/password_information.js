(function (SignIn) {
    function appendConstraintListElement(listNode, text)
    {
        let listElement = document.createElement('li');
        listElement.appendChild(document.createTextNode(text));

        listNode.appendChild(listElement);
    }

    SignIn.createPasswordInformationView = function() {
        let view = document.createElement('div');

        let constraintsParagraph = document.createElement('p');
        constraintsParagraph.appendChild(document.createTextNode(_d('sign_in', 'These are the constraints your password must comply to:')));

        let constraintsList = document.createElement('ul');
        let charactersFormat = _d('sign_in', 'Contain between %1 and %2 characters');
        appendConstraintListElement(constraintsList, Main.sprintf(charactersFormat, [SignIn.passwordMinLength, SignIn.passwordMaxLength]));
        appendConstraintListElement(constraintsList, _d('sign_in', 'Contain at least one lowercase character'));
        appendConstraintListElement(constraintsList, _d('sign_in', 'Contain at least one uppercase character'));
        appendConstraintListElement(constraintsList, _d('sign_in', 'Contain at least one digit'));

        let passwordPolicyLink          = document.createElement('p');
        let passwordPolicyFormat        = _d('sign_in', 'Please consult the %1 for more information.');
        let passwordPolicyUrl           = '/' + Main.getCurrentLanguage() + '/sign_in/password_policy';
        let passwordPolicyMessage       = '<a href="' + passwordPolicyUrl + '">' + _d('sign_in', 'password policy page') + '</a>';
        passwordPolicyLink.innerHTML    = Main.sprintf(passwordPolicyFormat, [passwordPolicyMessage]);

        // set up view hierarchy
        view.appendChild(constraintsParagraph);
        view.appendChild(constraintsList);
        view.appendChild(passwordPolicyLink);

        return view;
    };
})(window.SignIn = window.SignIn || {});