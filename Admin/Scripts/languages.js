(function (Language) {
    function setupCheckboxesListener()
    {
        let rows = document.querySelectorAll('#languages_table tr');
        for (var i = 0; i < rows.length; i++)
        {
            rows[i].addEventListener('click', rowListener);
        }
    }

    function rowListener(event)
    {
        let checkbox = this.children[0].children[0];
        if (event.target != checkbox)
        {
            checkbox.checked = !checkbox.checked;                   
        }

        this.className = checkbox.checked ? 'selected' : '';

        updateDeleteButton();
    }

    function getSeletectedLanguages()
    {
        var result = [];
        let checkboxes = document.querySelectorAll('#languages_table input');
        for (var i = 0; i < checkboxes.length; ++i)
        {
            if (checkboxes[i].checked)
            {
                result.push(i);
            }
        }
        return result;
    }

    function setupDeleteButton()
    {
        createHoverableButton(deleteSelectionButton, 'Delete', 'orange', 'rgb(255, 233, 199)');
        createDisableButton(deleteSelectionButton, 'gray', 'black', 'black');

        deleteSelectionButton.setDisabled(true);

        // confirm dialog
        let noHandler = new Dialog.ButtonHandler(createHoverableButton.bind(null, null, 'No', 'black', 'gray'));
        let yesHandler = new Dialog.ButtonHandler(createHoverableButton.bind(null, null, 'Yes', 'blue', 'lightblue'), function() {
            performDeletion();
            return true;
        });
        deleteSelectionButton.addEventListener('click', function (event){
            let selectedLanguages = getSeletectedLanguages();
            Dialog.appendDialogBox('Languages Deletion',
                'Are you sure you want to delete the selected languages (' + selectedLanguages.length + ') ?',
                [noHandler, yesHandler]);
        });
    }

    function updateDeleteButton()
    {
        let atLeastOneChecked = getSeletectedLanguages().length > 0;
        deleteSelectionButton.setDisabled(!atLeastOneChecked);
    }

    function performDeletion()
    {
        let rows = document.querySelectorAll('#languages_table tr');
        for (var i = rows.length - 1; i > 0; i--)
        {
            let checkbox = rows[i].children[0].children[0];
            if (checkbox.checked)
            {
                let idCell = rows[i].children[1];
                let id = parseInt(idCell.innerHTML);
                
                newLanguageTable.firstElementChild.removeChild(rows[i]);
                sendDeleteRequest(id);
            }
        }

        updateDeleteButton();
    }

    function sendDeleteRequest(languageId)
    {
        let request = new XMLHttpRequest();
        request.open('POST', '/Admin/Utils/delete_language.php', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.send('language_id=' + languageId);
    }

    Language.addLanguage = function(event) {
        // get the information
        let newLanguageName         = newLanguageNameField.value;
        let newLanguageShortName    = newLanguageShortNameField.value;

        // attempt to add the language
        if(canAddLanguage(newLanguageName, newLanguageShortName))
        {
            let request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                switch (this.readyState)
                {
                case XMLHttpRequest.OPENED:
                    newLanguageButton.disabled = true;
                    break;
                }
            };

            request.onloadend = function() {
                newLanguageButton.disabled = false;
                newLanguageError.style.visible = true;
                switch (this.status)
                {
                    // wrong arguments
                    case 400:
                        newLanguageError.innerHTML = this.responseText;
                        newLanguageNameField.focus();
                        break;
                    // success
                    case 200:
                        let newId = this.responseText;
                        newLanguageAddSuccess(newId, newLanguageName, newLanguageShortName);
                        break;
                    default:
                        break;
                }
            };

            request.open('POST', '/Admin/Utils/add_language.php', true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            request.send('new_language_name=' + newLanguageName + '&new_language_short_name=' + newLanguageShortName);
        }

        return false;
    };

    function canAddLanguage(newLanguageName, newLanguageShortName)
    {
        // regular expressions
        let spacesRegex = /\s/;

        let addLanguageTitle = 'Add New Language';

        // can not be empty
        if (newLanguageName == '')
        {
            Dialog.appendDialogBox(addLanguageTitle, 'The new language name may not be empty !');
        }
        else if (newLanguageShortName == '')
        {
            Dialog.appendDialogBox(addLanguageTitle, 'The new language short name may not be empty !');
        }
        // spaces
        else if (newLanguageName.match(spacesRegex))
        {
            Dialog.appendDialogBox(addLanguageTitle, 'The new language name can not contain spaces !');
        }
        else if (newLanguageShortName.match(spacesRegex))
        {
            Dialog.appendDialogBox(addLanguageTitle, 'The new language short name can not contain spaces !');
        }
        else
        {
            return true;
        }
        return false;
    }

    function newLanguageAddSuccess(languageId, languageName, languageShortName)
    {
        // hide the error paragraph
        newLanguageError.style.visible = 'hidden';

        // clear the fields
        newLanguageNameField.value      = '';
        newLanguageShortNameField.value = '';

        // focus the new language name field
        newLanguageNameField.focus();

        // add a new row
        let newRow = document.createElement('tr');

        let newCheckboxCell = document.createElement('td');
        let newCheckbox = document.createElement('input');
        newCheckbox.type = 'checkbox';
        newCheckbox.id = 'checkbox_' + (newLanguageTable.querySelectorAll('tr').length - 1);
        newCheckboxCell.appendChild(newCheckbox);
        let newIdCell = document.createElement('td');
        newIdCell.appendChild(document.createTextNode(languageId));
        let newNameCell = document.createElement('td');
        newNameCell.appendChild(document.createTextNode(languageName));
        let newShortNameCell = document.createElement('td');
        newShortNameCell.appendChild(document.createTextNode(languageShortName));

        newRow.appendChild(newCheckboxCell);
        newRow.appendChild(newIdCell);
        newRow.appendChild(newNameCell);
        newRow.appendChild(newShortNameCell);
        newRow.addEventListener('click', rowListener);

        newLanguageTable.appendChild(newRow);
    }

    let newLanguageNameField        = document.getElementById('new_language_name');
    let newLanguageShortNameField   = document.getElementById('new_language_short_name');
    let newLanguageTable            = document.querySelector('#languages_table');
    let newLanguageButton           = document.getElementById('new_language_button');
    let newLanguageError            = document.querySelector('.new_language_error');
    let deleteSelectionButton       = document.getElementById('remove_button');

    setupCheckboxesListener();
    setupDeleteButton();
})(window.Language = window.Language || {});