(function (NewSctSubject) {
    // -------------------------------------------------------------------------
    // SUBJECT STATUS
    // -------------------------------------------------------------------------
    NewSctSubject.updateSubjectStatusFunctions = [
        // questions count
        function () {
            return "Questions: " + NewSctSubject.questions.length;
        },
        // items count
        function () {
            var itemsCount = 0;
            for (var i = 0; i < NewSctSubject.questions.length; ++i)
            {
                itemsCount += NewSctSubject.questions[i].items.length;
            }
            return "Items: " + itemsCount;
        },
        // new items count
        function () {
            return 'New Question Items Count: ' + NewSctSubject.newQuestionItemsCount
        }
    ];

    NewSctSubject.updateSubjectStatus = function() {
        // questions
        var updateFunctions = NewSctSubject.updateSubjectStatusFunctions;

        var statusString = '';
        for (var i = 0; i < updateFunctions.length; ++i)
        {
            statusString += updateFunctions[i]();
            if (i < updateFunctions.length - 1)
            {
                statusString += ' | ';
            }
        }

        NewSctSubject.subjectStatusLabel.innerHTML = statusString;
    };

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------
    function populateMainToolbar()
    {
        let mainToolbar = document.getElementById('main_toolbar');
        let toolbarButtons = document.getElementById('main_toolbar_buttons');

        // add the add button
        let addQuestionsButton = createHoverableButton(undefined, 'Add', 'black', 'lightgray');
        addQuestionsButton.addEventListener('click', function (event) {
            NewSctSubject.refreshAddQuestionsView();

            Dialog.appendDialogBox('Add Questions', "Please enter the number of questions to add.",
                NewSctSubject.addQuestionsButtonHandlers, NewSctSubject.addQuestionsView);
        });
        toolbarButtons.appendChild(addQuestionsButton);

        NewSctSubject.addQuestionsButton = addQuestionsButton;

        // add the maximize all button
        let maximizeAllButton = createHoverableButton(undefined, 'Maximize All', 'green', 'lightgreen');
        maximizeAllButton.addEventListener('click', function(event) {
            for (let i = 0; i < NewSctSubject.subjectElement.questionElements.length; i++)
            {
                let question =  NewSctSubject.subjectElement.questionElements[i];
                question.foldHandler.changeFoldState(false);
            }
        });
        toolbarButtons.appendChild(maximizeAllButton);

        // add the minimize all button
        let minimizeAllButton = createHoverableButton(undefined, 'Minimize All', 'yellow', 'lightyellow');
        minimizeAllButton.addEventListener('click', function(event) {
            for (let i = 0; i < NewSctSubject.subjectElement.questionElements.length; i++)
            {
                let question =  NewSctSubject.subjectElement.questionElements[i];
                question.foldHandler.changeFoldState(true);
            }
        });
        toolbarButtons.appendChild(minimizeAllButton);

        // add the edit button
        let editButton = createHoverableButton(undefined, 'Edit', 'var(--hover-button-default-border)', 'var(--hover-button-default-bg)');
        toolbarButtons.appendChild(editButton);
        editButton.addEventListener('click', function(event) {
            NewSctSubject.refreshEditQuestionsView();
            
            Dialog.appendDialogBox('Edit Questions', undefined, NewSctSubject.editQuestionsHandlers, NewSctSubject.editQuestionsView);
        });

        // add the settings button
        let settingsButton = createHoverableButton(undefined, 'Settings', 'var(--hover-button-cancel-border)', 'var(--hover-button-cancel-bg)');
        toolbarButtons.appendChild(settingsButton);
        settingsButton.addEventListener('click', function (event) {
            NewSctSubject.refreshSettingsView();

            Dialog.appendDialogBox('Settings', undefined, NewSctSubject.settingsButtonHandlers, NewSctSubject.settingsView);
        });
    }

    function createNewQuestionButton()
    {
        var newQuestionButton = createHoverableButton(undefined, 'New Question', 'gray', 'lightgray');
        newQuestionButton.style.margin = 'auto';
        newQuestionButton.style.display = 'block';
        newQuestionButton.style.width = '40%';
        newQuestionButton.addEventListener('click', NewSctSubject.appendNewQuestion);
        NewSctSubject.subjectElement.parent.appendChild(newQuestionButton);
        NewSctSubject.newQuestionButton = newQuestionButton;

        var newSeparator = NewSctSubject.createSctSeparator();
        newSeparator.id = 'new_question_separator';

        NewSctSubject.subjectElement.parent.appendChild(newSeparator);
    }

    function createDocumentButtons()
    {
        var documentButtonsContainer = document.createElement('div');
        documentButtonsContainer.className = 'document_buttons_container';

        var buttonsInformation = [
            ['Delete', 'var(--hover-button-delete-border)', 'var(--hover-button-delete-bg)', confirmDelete],
            ['Save Draft', 'var(--hover-button-default-border)', 'var(--hover-button-default-bg)', function(){}],
            ['Validate', 'var(--hover-button-alternate-border)', 'var(--hover-button-alternate-bg)', function(event){
                let form = document.getElementById('sct_form');
                if (NewSctSubject.validate())
                {
                    form.submit();
                }
            }]
        ];

        for (var i = 0; i < buttonsInformation.length; ++i)
        {
            var currentInformation = buttonsInformation[i];

            var newButton = createHoverableButton(undefined, currentInformation[0], currentInformation[1], currentInformation[2]);
            newButton.style.width = (90 / buttonsInformation.length) + "%";
            newButton.addEventListener('click', currentInformation[3]);
            documentButtonsContainer.appendChild(newButton);
        }

        NewSctSubject.subjectElement.parent.appendChild(documentButtonsContainer);
    }

    NewSctSubject.appendNewQuestion = function() {
        if (NewSctSubject.questions.length > NewSctSubject.maxQuestions - 1)
        {
            return;
        }
        var newQuestionId = NewSctSubject.questions.length + 1;

        var newQuestion = new SctQuestion(newQuestionId, NewSctSubject.newQuestionItemsCount);
        NewSctSubject.questions.push(newQuestion);

        var newQuestionElement = new NewSctSubject.SctQuestionElement(newQuestion);
        NewSctSubject.subjectElement.questionElements.push(newQuestionElement);
        
        // create first items
        for (var i = 0; i < NewSctSubject.newQuestionItemsCount; i++)
        {
            newQuestionElement.addItem(new SctItem('', new SctData()));
        }

        // update the questions count label
        NewSctSubject.updateSubjectStatus();

        if (NewSctSubject.questions.length > NewSctSubject.maxQuestions - 1)
        {
            NewSctSubject.newQuestionButton.style.display = 'none';
            document.getElementById('new_question_separator').style.display = 'none';
            NewSctSubject.addQuestionsButton.style.display = 'none';
        }
    };

    // -------------------------------------------------------------------------
    // SHARED VARIABLES
    // -------------------------------------------------------------------------
    NewSctSubject.subjectStatusLabel                = document.getElementById('sct_subject_status');
    NewSctSubject.maxQuestions                      = 100;
    NewSctSubject.questions                         = [];
    NewSctSubject.newQuestionItemsCount             = 3;
    NewSctSubject.subjectElement.parent             = document.getElementById('sct_form');
    NewSctSubject.subjectElement.questionsParent    = document.getElementById('sct_questions');
    NewSctSubject.subjectElement.questionElements   = [];

    // append the first question
    NewSctSubject.appendNewQuestion();

    // populate the main toolbar
    populateMainToolbar();

    // create the new question button
    createNewQuestionButton();

    // create the document buttons: delete, save draft, validate
    createDocumentButtons();

    // confirm close
    /*window.addEventListener('beforeunload', function(event) {
        event.preventDefault()
        event.returnValue = '';
    });*/

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------
    function confirmDelete()
    {
        let noHandler   = new Dialog.ButtonHandler(createHoverableButton.bind(null, undefined, 'No', 'var(--hover-button-cancel-border)', 'var(--hover-button-cancel-bg'), undefined);
        var yesHandler  = new Dialog.ButtonHandler(createHoverableButton.bind(null, undefined, 'Yes', 'var(--hover-button-default-border)', 'var(--hover-button-default-bg'), function() {
            window.location.href = "home";
        });
        Dialog.appendDialogBox('Subject Deletion', 'Are you sure you want to delete the subject ?', [noHandler, yesHandler]);
    }
})(window.NewSctSubject = window.NewSctSubject || {});