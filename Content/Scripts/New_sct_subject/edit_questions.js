(function(NewSctSubject) {
    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------
    function createToolbarSelectAllButton()
    {
        let selectAllButton = createHoverableButton(undefined, _d('new_sct_subject', 'Select All'), 'green', 'lightgreen');
        selectAllButton.addEventListener('click', function (event) {
            let newSelected = !allQuestionsSelected();
            let checkboxes = editQuestionsElement.editQuestionsView.querySelectorAll('[type=checkbox]');

            for (var i = 0; i < selection.length; i++)
            {
                checkboxes[i].checked = newSelected;
                selection[i] = newSelected;
            }

            refreshSelection();
        });
        return selectAllButton;
    }

    function createToolbarDeleteButton()
    {
        let deleteButton = createHoverableButton(undefined, _d('new_sct_subject', 'Delete'), 'var(--hover-button-delete-border)', 'var(--hover-button-delete-bg)');
        deleteButton = createDisableButton(deleteButton, 'lightgray', 'black', 'gray');
        deleteButton.addEventListener('click', function (event) {
            let cancelHandler = new Dialog.ButtonHandler(
                createHoverableButton.bind(null, undefined, _('No'), 'var(--hover-button-cancel-border)', 'var(--hover-button-cancel-bg)'));
            let okHandler = new Dialog.ButtonHandler(
                createHoverableButton.bind(null, undefined, _('Yes'), 'var(--hover-button-delete-border)', 'var(--hover-button-delete-bg)'),
                function() {
                    removeQuestions(questionsToDelete);
                    return true;
                });

            let questionsToDelete = []
            for (var i = 0; i < selection.length; ++i)
            {
                if (selection[i])
                {
                    questionsToDelete.push(i + 1);
                }
            }

            let deleteMessageFormat = _n('new_sct_subject', 'Are you sure you want to delete the selected question ?', questionsToDelete.length)
            let deleteMessage = Main.sprintf(deleteMessageFormat, questionsToDelete.length);
            Dialog.appendDialogBox(_d('new_sct_subject', 'Questions Deletion'), deleteMessage, [cancelHandler, okHandler]);
        });
        return deleteButton;
    }

    function createToolbarButtons()
    {
        let toolbarButtons = [selectAllButton, deleteQuestionsButton];
        for (var i = 0; i < toolbarButtons.length; i++)
        {
            toolbarButtons[i].style.width = parseInt(90 / toolbarButtons.length) + '%';
        }
        return toolbarButtons;
    }

    function createEditToolbar()
    {
        let toolbar = document.createElement('div');
        toolbar.id = 'edit_questions_toolbar';

        let buttonsContainer = document.createElement('div');
        buttonsContainer.className = 'buttons_container';

        // hierarchy
        Main.appendChildren(buttonsContainer, createToolbarButtons());
        toolbar.appendChild(buttonsContainer);

        return toolbar;
    }

    function createQuestionsListHeaderRow()
    {
        let questionsListHeader = document.createElement('tr');
        questionsListHeader.className = 'questions_edit_header_row';

        // <checkbox> <question number> <question wording> <buttons>
        let questionCell = document.createElement('th');
        questionCell.colSpan = 2;
        questionCell.appendChild(document.createTextNode(_d('new_sct_subject', 'edit_questions_question')));

        let wordingCell = document.createElement('th');
        wordingCell.appendChild(document.createTextNode(_d('new_sct_subject', 'edit_questions_wording')));

        let buttonsCell = document.createElement('th');
        buttonsCell.appendChild(document.createTextNode(_d('new_sct_subject', 'edit_questions_actions')));
        buttonsCell.className = "question_edit_buttons"

        Main.appendChildren(questionsListHeader, [questionCell, wordingCell, buttonsCell]);

        return questionsListHeader;
    }

    function createQuestionEditButtons(questionNumber)
    {
        let buttons = [];
        let deleteButton = createHoverableButton(undefined, _d('new_sct_subject', 'Delete'), 'var(--hover-button-delete-border)', 'var(--hover-button-delete-bg');
        deleteButton.id = 'edit_delete_question_' + questionNumber;
        deleteButton.addEventListener('click', function (event) {
            let questionNumber = parseInt(event.target.id.replace(/.*_/, ''));
            
            removeQuestions([questionNumber]);
        });

        buttons.push(deleteButton);

        return buttons;
    }

    function createQuestionListRow(questionNumber)
    {
        let row = document.createElement('tr');

        // <checkbox> <question number> <question wording> <buttons>
        let checkboxCell = document.createElement('td');
        let checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = "question_checkbox_" + questionNumber;
        checkboxCell.appendChild(checkbox);
        checkbox.addEventListener('change', function(event) {
            let id = parseInt(event.target.id.replace(/.*_/, ''));
            selection[id - 1] = event.target.checked;

            refreshSelection();
        });

        let questionCell = document.createElement('td');
        questionCell.className = "question_number";
        questionCell.appendChild(document.createTextNode(questionNumber));

        let wordingCell = document.createElement('td');
        wordingCell.className = "wording";

        let buttonsCell = document.createElement('td');
        buttonsCell.className = 'question_edit_buttons';
        Main.appendChildren(buttonsCell, createQuestionEditButtons(questionNumber));

        Main.appendChildren(row, [checkboxCell, questionCell, wordingCell, buttonsCell]);
        return row;
    }

    function createEditQuestionsElement()
    {
        let editQuestionsView = document.createElement('div');
        editQuestionsView.id = 'sct_edit_questions_view';

        // toolbar
        let toolbar = createEditToolbar();

        // questions
        let questionsList = document.createElement('table');
        questionsList.id = 'questions_list';
        let questionsListHeaderRow = createQuestionsListHeaderRow();

        // set up hierarchy
        questionsList.appendChild(questionsListHeaderRow);
        editQuestionsView.appendChild(toolbar);
        editQuestionsView.appendChild(questionsList);

        return {editQuestionsView: editQuestionsView, questionsList: questionsList};
    }
    
    function createCancelEditQuestionsHandler()
    {
        return new Dialog.ButtonHandler(createHoverableButton.bind(null, undefined, _('Cancel'), 'var(--hover-button-cancel-border)', 'var(--hover-button-cancel-bg)'));
    }

    function createDoneEditQuestionsHandler()
    {
        return new Dialog.ButtonHandler(createHoverableButton.bind(null, undefined, _('Ok'), 'var(--hover-button-default-border)', 'var(--hover-button-default-bg)'));
    }

    // -------------------------------------------------------------------------
    // REFRESH
    // -------------------------------------------------------------------------
    // returns the new rows
    function updateRowsCount(questionsList)
    {
        let newRowsCount = NewSctSubject.questions.length;
        let currentRows = Array.from(questionsList.querySelectorAll('tr'));
        currentRows.splice(0,1);

        // update selections
        selection = [];
        for (var i = 0; i < newRowsCount; i++)
        {
            selection.push(false);
        }

        // append rows
        if (newRowsCount > currentRows.length)
        {
            for (var i = currentRows.length; i < newRowsCount; i++)
            {
                let newRow = createQuestionListRow(i + 1);
                questionsList.appendChild(newRow);
                currentRows.push(newRow);
            }
        }
        // remove rows
        else if (newRowsCount < currentRows.length)
        {
            for (var i = currentRows.length - 1; i > newRowsCount - 1; i--)
            {
                questionsList.removeChild(currentRows[i]);
            }
            currentRows.splice(newRowsCount);
        }
        return currentRows;
    }

    function refreshQuestionRows(questionRows)
    {
        for (var i = 0; i < NewSctSubject.questions.length && i < questionRows.length; i++)
        {
            var question = NewSctSubject.questions[i];

            questionRows[i].querySelector('.wording').innerHTML = question.wording;
            questionRows[i].querySelector('[type=checkbox]').checked = selection[i];

            if (i == 0)
            {
                questionRows[i].querySelector('#edit_delete_question_1').style.display = (questionRows.length == 1 ? 'none' : '');
            }
        }
    }

    function refreshEditQuestionsList()
    {
        let questionRows = updateRowsCount(editQuestionsElement.questionsList);
        refreshQuestionRows(questionRows);
    }

    function allQuestionsSelected()
    {
        for (var i = 0; i < selection.length; i++)
        {
            if (!selection[i])
            {
               return false;
            }
        }
        return true;
    }

    function noQuestionSelected()
    {
        for (var i = 0; i < selection.length; i++)
        {
            if (selection[i])
            {
               return false;
            }
        }
        return true;
    }

    function refreshSelection()
    {
        let allSelected = allQuestionsSelected();
        selectAllButton.value = allSelected ? _d('new_sct_subject', 'Unselect All') : _d('new_sct_subject', 'Select All');

        if (selection.length < 2 || allSelected || noQuestionSelected())
        {
            deleteQuestionsButton.setDisabled(true);
        }
        else
        {
            deleteQuestionsButton.setDisabled(false);
        }
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------
    function removeQuestions(questionNumbers)
    {
        let questionRows = Array.from(editQuestionsElement.questionsList.querySelectorAll('tr'));
        
        for (var i = 0; i < questionNumbers.length; i++)
        {
            let questionToDelete = questionNumbers[questionNumbers.length - 1 - i];
            editQuestionsElement.questionsList.removeChild(questionRows[questionRows.length - i - 1]);
            NewSctSubject.deleteSctQuestion(questionToDelete);

            selection.splice(questionToDelete - 1, 1);
        }
        questionRows.splice(questionRows.length - questionNumbers.length);
        questionRows.splice(0, 1);

        refreshQuestionRows(questionRows);
    }

    NewSctSubject.refreshEditQuestionsView = function() {
        refreshEditQuestionsList();
        refreshSelection();
    };

    let selectAllButton = createToolbarSelectAllButton();
    let deleteQuestionsButton = createToolbarDeleteButton();
    let editQuestionsElement = createEditQuestionsElement();
    let selection = [];
    NewSctSubject.editQuestionsView = editQuestionsElement.editQuestionsView;
    NewSctSubject.editQuestionsHandlers = [createCancelEditQuestionsHandler(), createDoneEditQuestionsHandler()];

})(window.NewSctSubject = window.NewSctSubject || {});