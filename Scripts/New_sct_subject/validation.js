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
        var result = [];
        for (let i = 0; i < NewSctSubject.questions.length; ++i)
        {
            let questionNumber = i + 1;
            let question = NewSctSubject.questions[i];
            let wordingElement = document.getElementById('sct_wording_' + questionNumber);
            if (question.wording == '')
            {
                result.push(new SubjectError('Empty Wording', 'Question Wording ' + questionNumber, 'sct_wording_' + questionNumber, questionNumber));

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
                        'Question ' + questionNumber + ' - ' + elementName + ' ' + itemNumber,
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
        var result = checkWordings();
        result = result.concat(checkItemElement('hypothesis', 'Hypothesis', item => item.hypothesis == '', 'Empty Hypothesis'));
        result = result.concat(checkItemElement('data_text', 'Data Text', item => item.newData.associatedData == '', 'Empty Data Text'));
        result = result.concat(checkItemElement('data_image', 'Data Image', item => item.newData.associatedData == '', 'Image Not Specified'));
        result = result.concat(checkItemElement('data_volume', 'Data Volume', item => item.newData.associatedData == '', 'Volume Not Specified'));

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

        errorsCount.innerHTML = errors.length + ' error(s) detected.';

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