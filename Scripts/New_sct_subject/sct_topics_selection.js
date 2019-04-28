(function(NewSctSubject) {
    function createSctTopicsSelection()
    {
        var sctTopicsSelection = document.createElement('ul');
        sctTopicsSelection.className = 'sct_topics_selection';

        for (var i = 0; i < NewSctSubject.sctTopics.length; i++)
        {
            var currentTopic = NewSctSubject.sctTopics[i];

            var newListItem = document.createElement('li');
            
            // check box
            var newTopicElement = document.createElement('input');
            newTopicElement.id = 'sct_topic_' + currentTopic.id;
            newTopicElement.type = 'checkbox';
            newTopicElement.value = currentTopic.name;

            // label
            var newTopicLabel = document.createElement('label');
            newTopicLabel.htmlFor = 'sct_topic_' + currentTopic.id;
            newTopicLabel.appendChild(document.createTextNode(currentTopic.name));

            newListItem.appendChild(newTopicElement);
            newListItem.appendChild(newTopicLabel);

            sctTopicsSelection.appendChild(newListItem);
        }

        return sctTopicsSelection;
    }

    function createCancelTopicsSelectionHandler()
    {
        return new Dialog.ButtonHandler(createHoverableButton.bind(null, undefined, 'Cancel', 'gray', 'lightgray'));
    }

    function createDoneTopicsSelectionHandler()
    {
        return new Dialog.ButtonHandler(
            createHoverableButton.bind(null, undefined, 'Done', 'var(--hover-button-default-border)', 'var(--hover-button-default-bg)'),
            function()
            {
                refreshSelectedTopics();
                return true;
            }
        );
    }

    class SctTopicsSelection
    {
        constructor()
        {
            this.node = createSctTopicsSelection();
            this.checkboxes = this.node.querySelectorAll('input[type=checkbox]');
        }

        get selectedTopics()
        {
            return Array.from(this.checkboxes).map(function(checkbox) { return checkbox.checked; });
        }

        update(question)
        {
            for (var i = 0; i < this.checkboxes.length; i++)
            {
                var checkbox = this.checkboxes[i];
                checkbox.checked = question.selectedTopics[i];
            }
        }
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------
    function refreshSelectedTopics()
    {
        var question        = NewSctSubject.questions[NewSctSubject.currentSctQuestionNumber - 1];
        var questionElement = NewSctSubject.subjectElement.questionElements[NewSctSubject.currentSctQuestionNumber - 1];
    
        var selectedTopics = NewSctSubject.topicsSelection.selectedTopics;
        for (var i = 0; i < selectedTopics.length; i++)
        {
            var selectedTopic = selectedTopics[i];
            question.selectedTopics[i] = selectedTopic;
        }
        questionElement.updateTopics(question);
    }

    NewSctSubject.topicsSelection = new SctTopicsSelection();
    NewSctSubject.subjectElement = {
        topicsSelection: NewSctSubject.topicsSelection.node,
        topicsSelectionButtonHandlers: [createCancelTopicsSelectionHandler(),
            createDoneTopicsSelectionHandler()
        ]
    };

    /*NewSctSubject.subjectElement.sctTopicsDone.addEventListener('click', function (event) {
        Main.closeDialogBox();
    });*/
})(window.NewSctSubject = window.NewSctSubject || {});
