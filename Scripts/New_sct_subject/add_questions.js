(function(NewSctSubject) {
    function createCancelAddQuestionsButtonHandler()
    {
        return new Dialog.ButtonHandler(createHoverableButton.bind(null, undefined, 'Cancel', 'var(--hover-button-cancel-border)', 'var(--hover-button-cancel-bg)'));
    }

    function createOkAddQuestionsButtonHandler()
    {
        return new Dialog.ButtonHandler(createHoverableButton.bind(null, undefined, 'Ok', 'var(--hover-button-default-border)', 'var(--hover-button-default-bg)'),
            function() {
                let questionsToAdd = parseInt(newQuestionsCount.value);
                if (questionsToAdd < 1 || questionsToAdd > NewSctSubject.maxQuestions - NewSctSubject.questions.length)
                {
                    return false;
                }

                // add the new questions
                for (var i = 0; i < questionsToAdd && NewSctSubject.questions.length < NewSctSubject.maxQuestions; i++)
                {
                    NewSctSubject.appendNewQuestion();
                }
                return true;
            }
        );
    }

    function createAddQuestionsView()
    {
        let addQuestionsView = document.createElement('div');

        let addQuestionsLabel = document.createElement('label');
        addQuestionsLabel.appendChild(document.createTextNode('Number of questions to add:'));
        addQuestionsLabel.htmlFor = 'add_questions_count';

        addQuestionsView.appendChild(addQuestionsLabel);
        addQuestionsView.appendChild(newQuestionsCount);

        return addQuestionsView;
    }

    function createNewQuestionsCount()
    {
        let newQuestionsCount = document.createElement('input');
        newQuestionsCount.id = 'add_questions_count';
        newQuestionsCount.type = 'number';
        newQuestionsCount.min = 1;
        return newQuestionsCount;
    }

    let newQuestionsCount = createNewQuestionsCount();

    NewSctSubject.addQuestionsView      = createAddQuestionsView();
    NewSctSubject.addQuestionsButtonHandlers = [
        createCancelAddQuestionsButtonHandler(),
        createOkAddQuestionsButtonHandler()
    ];

    NewSctSubject.refreshAddQuestionsView = function() {
        newQuestionsCount.value = 1;
        newQuestionsCount.max = NewSctSubject.maxQuestions - NewSctSubject.questions.length;
    };
})(window.NewSctSubject = window.NewSctSubject || {});