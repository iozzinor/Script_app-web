(function(NewSctSubject) {
    // -------------------------------------------------------------------------
    // ERROR
    // -------------------------------------------------------------------------
    class SubjectError
    {
        constructor(message, elementName, elementId, questionNumber, itemNumber)
        {
            this.message        = message;
            this.elementName    = elementName;
            this.elementId      = elementId;
            this.questionNumber = questionNumber;
            this.itemNumber     = itemNumber;
        }
    }

    // -------------------------------------------------------------------------
    // VALIDATION
    // -------------------------------------------------------------------------
    function checkWordings()
    {
        let errorDescriptionEmptyFormat = _d('new_sct_subject', 'error_description_empty_format');
        let errorDataFormat = _d('new_sct_subject', 'error_data_format');
        var result = [];
        for (let i = 0; i < NewSctSubject.questions.length; ++i)
        {
            let questionNumber = i + 1;
            let question = NewSctSubject.questions[i];
            let wordingElement = document.getElementById('sct_wording_' + questionNumber);
            if (question.wording == '')
            {
                let errorMessage = Main.sprintf(errorDescriptionEmptyFormat, _d('new_sct_subject', 'edit_questions_wording'));
                let errorLink =  Main.sprintf(errorDataFormat, [questionNumber, _d('new_sct_subject', 'edit_questions_wording')]);
                result.push(new SubjectError(errorMessage, errorLink, 'sct_wording_' + questionNumber, questionNumber));

                wordingElement.className = 'sct_wording_error';
            }
            else
            {
                wordingElement.className = '';
            }
        }
        return result;
    }

    function checkItemElement(elementIdBaseName, elementName, isIncorrect, errorMessage)
    {
        var result = [];
        let elementErrorClassName = 'sct_item_' + elementIdBaseName + '_error';
        let errorDataTypeFormat = _d('new_sct_subject', 'error_data_type_format');
        for (let i = 0; i < NewSctSubject.questions.length; ++i)
        {
            let questionNumber = i + 1;
            let question = NewSctSubject.questions[i];
            let baseElementId = 'sct_item_' + elementIdBaseName + '_' + questionNumber + '_';

            for (var j = 0; j < question.items.length; j++)
            {
                let itemNumber = j + 1;
                let elementId = baseElementId + itemNumber;
                let element = document.getElementById(elementId);
                if (!element)
                {
                    continue;
                }
                if (isIncorrect(question.items[j]))
                {
                    result.push(new SubjectError(errorMessage,
                        Main.sprintf(errorDataTypeFormat, [questionNumber, elementName, itemNumber]),
                        elementId,
                        questionNumber,
                        itemNumber));
    
                    element.className = elementErrorClassName;
                }
                else
                {
                    element.className = '';
                }
            }
        }
        return result;
    }
    
    function getErrors()
    {
        let errorDescriptionEmptyFormat = _d('new_sct_subject', 'error_description_empty_format');
        let errorDescriptionNotSpecifiedFormat = _d('new_sct_subject', 'error_description_not_specified_format');

        var result = checkWordings();
        result = result.concat(checkItemElement('hypothesis', _d('new_sct_subject', 'error_hypothesis'), item => item.hypothesis == '', Main.sprintf(errorDescriptionEmptyFormat, _d('new_sct_subject', 'error_hypothesis'))));
        result = result.concat(checkItemElement('data_text',  _d('new_sct_subject', 'error_data_type_text'), item => item.newData.associatedData == '', Main.sprintf(errorDescriptionEmptyFormat, _d('new_sct_subject', 'error_data_type_text'))));
        result = result.concat(checkItemElement('data_image',  _d('new_sct_subject', 'error_data_type_image'), item => item.newData.associatedData == '', Main.sprintf(errorDescriptionNotSpecifiedFormat, _d('new_sct_subject', 'error_data_type_image'))));
        result = result.concat(checkItemElement('data_volume',  _d('new_sct_subject', 'error_data_type_volume'), item => item.newData.associatedData == '', Main.sprintf(errorDescriptionNotSpecifiedFormat, _d('new_sct_subject', 'error_data_type_volume'))));

        result.sort(function (a, b) {
            if (a.questionNumber != b.questionNumber)
            {
                return a.questionNumber - b.questionNumber;
            }
            return a.itemNumber - b.itemNumber;
        });

        return result;
    }

    function displayErrors(errors)
    {
        let errorsSection = document.getElementById('errors_section');
        let errorsCount = document.getElementById('errors_count');
        let errorsList = document.getElementById('errors_list');

        Main.removeAllChildren(errorsList);

        let errorsMessage = Main.sprintf(_dn('new_sct_subject', 'one error detected.', '%1 errors detected.', errors.length), errors.length);
        errorsCount.innerHTML = errorsMessage;

        for (var i = 0; i < errors.length; ++i)
        {
            let error = errors[i];

            var newError = document.createElement('li');
            newError.appendChild(document.createTextNode(error.message + ', '));
            
            var errorLink = document.createElement('a');
            errorLink.appendChild(document.createTextNode(error.elementName));
            errorLink.href = "#" + error.elementId;
            errorLink.addEventListener('click', function(event) {
                let questionIndex = error.questionNumber - 1;
                let questionElement = NewSctSubject.subjectElement.questionElements[questionIndex];
                
                if (questionElement.foldHandler.folded)
                {
                    questionElement.foldHandler.changeFoldState(false);
                }
            });

            newError.appendChild(errorLink);

            errorsList.appendChild(newError);
        }

        errorsSection.style.display = (errors.length > 0 ? 'block' : 'none');
    }

    function hideErrors()
    {
        let errorsSection = document.getElementById('errors_section');
        errorsSection.style.display = 'none';
    }

    NewSctSubject.validate = function() {
        let errors = getErrors();
        if (errors.length > 0)
        {
            displayErrors(errors);
        }
        else
        {
            hideErrors();
        }

        return errors.length < 1;
    };

    // -------------------------------------------------------------------------
    // SEND STATE
    // -------------------------------------------------------------------------
    let SendState = {
        NONE:       0,
        ERROR:      1,
        SENT:       2,
        SENDING:    4,
        ABORTED:    8
    };

    // -------------------------------------------------------------------------
    // SEND
    // -------------------------------------------------------------------------
    function createSendProgressStatus()
    {
        let status = document.createElement('p');
        return status;
    }

    function createSendProgressBar()
    {
        let documentStyle = getComputedStyle(document.documentElement);
        let progressColor = documentStyle.getPropertyValue('--progress-bar-progress');
        let remainingColor = documentStyle.getPropertyValue('--progress-bar-remaining');
        let progressBar = new ProgressBar.Bar(progressColor, remainingColor);
        return progressBar;
    }

    function createSendProgressView()
    {
        let result = document.createElement('div');
        result.id = 'send_progress_view';
        return result;
    }

    function createSendProgressElement()
    {
        let element = {};
        element.progressView = createSendProgressView();

        // progress status
        element.progressStatus = createSendProgressStatus();
        element.progressView.appendChild(element.progressStatus);

        // progress bar
        element.progressBar = createSendProgressBar();
        element.progressView.appendChild(element.progressBar.container);

        return element;
    }

    function getLanguage()
    {
        let language = 'en';
        for (var i = 0; i < NewSctSubject.sctLanguages.length; ++i)
        {
            if (NewSctSubject.sctLanguages[i].id == NewSctSubject.sctLanguageId)
            {
                language = NewSctSubject.sctLanguages[i].shortName;
                break;
            }
        }
        return language;
    }

    function getFormData()
    {
        let formData = new FormData();

        formData.append('language', getLanguage());

        formData.append('questions_count', NewSctSubject.questions.length);

        let itemsCount = 0;
        for (var i = 0; i < NewSctSubject.questions.length; ++i)
        {
            itemsCount += NewSctSubject.questions[i].items.length;
        }
        formData.append('total_items_count', itemsCount);

        // add questions
        for (var i = 0; i < NewSctSubject.questions.length; ++i)
        {
            let currentQuestion = NewSctSubject.questions[i];
            addQuestionToFormData(i + 1, currentQuestion, formData);
        }

        return formData;
    }

    function addQuestionToFormData(questionNumber, question, formData)
    {
        // type
        let sctType = 'Diagnostic';
        for (var i = 0; i < NewSctSubject.sctTypes.length; ++i)
        {
            if (NewSctSubject.sctTypes[i].id == question.typeId)
            {
                sctType = NewSctSubject.sctTypes[i].identifier;
            }
        }
        formData.append('type_' + questionNumber, sctType);

        // topics
        let topicsString = '';
        for (var i = 0; i < NewSctSubject.sctTopics.length; ++i)
        {
            if (question.selectedTopics[i])
            {
                if (topicsString !== '')
                {
                    topicsString += ';';
                }
                topicsString += NewSctSubject.sctTopics[i].identifier;
            }
        }
        formData.append('topics_' + questionNumber, topicsString);

        // wording
        formData.append('wording_' + questionNumber, question.wording);

        // items
        formData.append('items_' + questionNumber, question.items.length);
        addItemsToFormData(questionNumber, question, formData);
    }

    function addItemsToFormData(questionNumber, question, formData)
    {
        for (var i = 0; i < question.items.length; ++i)
        {
            let item = question.items[i];

            formData.append('question_hypothesis_' + questionNumber + '_' + (i + 1), item.hypothesis);

            // check for file
            let inputId = 'sct_item_data_' + item.newData.dataType.nameCode.toLowerCase() + '_' + questionNumber + '_' + (i + 1);
            let input = document.getElementById(inputId);
            formData.append('question_new_data_type_' + questionNumber + '_' + (i + 1), item.newData.dataType.nameCode.toLowerCase());
            if (input.type === 'file')
            {
                formData.append('question_new_data_' +  questionNumber + '_' + (i + 1), input.files[0]);
            }
            else
            {
                formData.append('question_new_data_' + questionNumber + '_' + (i + 1), item.newData.dataType.nameCode.toLowerCase() + ':' + item.newData.associatedData);
            }
        }
    }

    NewSctSubject.sendSubject = function() {
        // reset the progress status
        progressElement.progressStatus.innerHTML = _d('new_sct_subject', 'The SCT is being sent...');

        // reset the progress bar
        progressElement.progressBar.setProgress(0);
        progressElement.progressBar.container.style.visibility = 'visible';

        // reset the alert box button
        Dialog.updateTitle(0, _('Cancel'));

        // get the form
        let formData = getFormData();
        
        // make the request
        sendState = SendState.SENDING;
        sendRequest = new XMLHttpRequest();
        sendRequest.onloadend = function() {
            if (sendState == SendState.ABORTED)
            {
                return;
            }

            if (this.status != 200)
            {
                progressElement.progressStatus.innerHTML = _d('new_sct_subject', 'An error occured.');
                progressElement.progressBar.container.style.visibility = 'hidden';
                Dialog.updateTitle(0, _d('new_sct_subject', 'Retry'));
                sendState = SendState.ERROR;
            }
            else
            {
                progressElement.progressStatus.innerHTML = _d('new_sct_subject', 'The SCT has been sent!');
                Dialog.updateTitle(0, _('Ok'));
                sendState = SendState.SENT;
            }
        };
        sendRequest.onprogress = function (event) {
            let percentComplete = event.loaded / event.total * 100;
            progressElement.progressBar.setProgress(percentComplete);
        };
        sendRequest.open('POST', '/en/new_sct_subject/add_new', true);
        sendRequest.send(formData);

        let cancelButtonHandler = new Dialog.ButtonHandler(
            createHoverableButton.bind(null, null, _('Cancel'), 'var(--hover-button-cancel-border)', 'var(--hover-button-cancel-bg)'),
            function () {
                switch (sendState)
                {
                case SendState.SENDING:
                    sendRequest.abort();
                    sendState = SendState.ABORTED;
                    dialogOpened = false;
                    return true;

                case SendState.SENT:
                    dialogOpened = false;
                    // redirect to the home page
                    window.location.replace('/' + getLanguage() + '/home');

                    return true;

                case SendState.ERROR:
                    NewSctSubject.sendSubject();
                    return false;

                default:
                    break;
                }

                return false;
            });

        // display the dialog box
        if (!dialogOpened)
        {
            dialogOpened = true;
            Dialog.appendDialogBox(
                _d('new_sct_subject', 'Sending the SCT'),
                null,
                [cancelButtonHandler],
                progressElement.progressView);
        }
    };

    let progressElement = createSendProgressElement();
    let sendRequest = new XMLHttpRequest();
    let sendState = SendState.NONE;
    let dialogOpened = false;
})(window.NewSctSubject = window.NewSctSubject || {});