(function(NewSctSubject) {
    function createCancelSettingsButtonHandler()
    {
        return new Dialog.ButtonHandler(createHoverableButton.bind(null, undefined, _('Cancel'), 'var(--hover-button-cancel-border)', 'var(--hover-button-cancel-bg)'));
    }

    function createOkSettingsButtonHandler()
    {
        return new Dialog.ButtonHandler(
            createHoverableButton.bind(null, undefined, _('Ok'), 'var(--hover-button-default-border)', 'var(--hover-button-default-bg)'),
            function (event) {
                let newValue    = parseInt(newQuestionItemsCount.value);
                let min         = parseInt(newQuestionItemsCount.min);
                let max         = parseInt(newQuestionItemsCount.max);
                if (isNaN(newValue) || newValue < min || newValue > max)
                {
                    return false;
                }
    
                // save settings
                NewSctSubject.newQuestionItemsCount = newValue;
                NewSctSubject.updateSubjectStatus();
                return true;
            }
        );
    }

    function createNewQuestionItemsCount()
    {
        let newQuestionItemsCount = document.createElement('input');
        newQuestionItemsCount.id    = 'new_question_items_count';
        newQuestionItemsCount.type  = 'number';
        newQuestionItemsCount.min   = 3;
        newQuestionItemsCount.max   = 10;
        return newQuestionItemsCount;
    }

    function createSettingsView()
    {
        let settingsView = document.createElement('div');
        settingsView.id = 'new_sct_subject_settings_view';

        // new question items count
        let newQuestionItemsCountLabel = document.createElement('label');
        newQuestionItemsCountLabel.id = 'new_question_items_count_label';
        newQuestionItemsCountLabel.htmlFor = 'new_question_items_count';
        newQuestionItemsCountLabel.appendChild(document.createTextNode(_d('new_sct_subject', 'New Question Items Count:')));
        
        settingsView.appendChild(newQuestionItemsCountLabel);
        settingsView.appendChild(newQuestionItemsCount);

        return settingsView
    }

    NewSctSubject.refreshSettingsView = function() {
        newQuestionItemsCount.value = NewSctSubject.newQuestionItemsCount;
    };

    let newQuestionItemsCount = createNewQuestionItemsCount();

    NewSctSubject.settingsButtonHandlers = [
        createCancelSettingsButtonHandler(),
        createOkSettingsButtonHandler()
     ];
    NewSctSubject.settingsView      = createSettingsView();

})(window.NewSctSubject = window.NewSctSubject || {});