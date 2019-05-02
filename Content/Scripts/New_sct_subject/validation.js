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

    NewSctSubject.validate = function() {
        let errors = getErrors();
        if (errors.length > 0)
        {
            displayErrors(errors);
        }

        return errors.length < 1;
    };
})(window.NewSctSubject = window.NewSctSubject || {});