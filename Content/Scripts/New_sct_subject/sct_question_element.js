(function(NewSctSubject)
{
    // -------------------------------------------------------------------------
    // SCT SEPARATOR
    // -------------------------------------------------------------------------
    NewSctSubject.createSctSeparator = function() {
        var newSeparator = document.createElement('div');
        newSeparator.className = "sct_question_separator";
        return newSeparator;
    };

    // -------------------------------------------------------------------------
    // NEW QUESTION
    // -------------------------------------------------------------------------
    function createQuestionDeleteButton(question)
    {
        var deleteButton = createHoverableButton(undefined, _d('new_sct_subject', 'Delete Question'), 'red', 'var(--hover-button-delete-bg)');
        deleteButton.addEventListener('click', function(event) {
            NewSctSubject.deleteSctQuestion(question.id);
        });
        if (NewSctSubject.questions.length == 1)
        {
            deleteButton.style.display = 'none';
        }
        else
        {
            NewSctSubject.subjectElement.questionElements[0].deleteButton.style.display = '';
        }
        return deleteButton;
    }

    function createToolboxButtons(question, questionElement)
    {
        var questionToolboxButtons = document.createElement('div');
        questionToolboxButtons.className = 'sct_question_toolbox_buttons';

        // delete
        var questionDeleteButton = createQuestionDeleteButton(question);
        questionElement.deleteButton = questionDeleteButton;

        questionToolboxButtons.appendChild(questionDeleteButton);

        return questionToolboxButtons;
    }

    function createFoldableContainer(question, questionElement)
    {
        let foldableContainer = document.createElement('div');
        foldableContainer.className = 'foldable_container';

        // number
        var questionNumberElement = document.createElement('p');
        questionNumberElement.appendChild(document.createTextNode(Main.sprintf(_d('new_sct_subject', 'Question %1'), question.id)));
        questionNumberElement.draggable = 'true';
        questionNumberElement.ondragstart = function(event) {
            event.dataTransfer.setData('question_number', question.id);
        };

        // disclosure
        let questionDisclosureButton            = document.createElement('div');
        questionDisclosureButton.className      = 'disclosure_disclosed';

        foldableContainer.appendChild(questionNumberElement);
        foldableContainer.appendChild(questionDisclosureButton);

        // disclosure toggle
        let foldHandler = {
            folded: false,
            targetNode: {},
            foldEvent: function (event) {
                this.folded = !this.folded;
    
                questionDisclosureButton.className = this.folded ? 'disclosure_disclose' : 'disclosure_disclosed';

                this.targetNode.style.display = this.folded ? 'none' : 'block';
            },
            changeFoldState: function(newState){
                if (this.folded == newState)
                {
                    return;
                }
                this.foldEvent();
            }
        }
        foldableContainer.addEventListener('click', function(event) { foldHandler.foldEvent(event) });

        questionElement.numberField = questionNumberElement;
        questionElement.foldHandler = foldHandler;

        return foldableContainer;
    }

    function createQuestionToolbox(question, questionElement)
    {
        var questionToolbox = document.createElement('div');
        questionToolbox.className = 'sct_question_toolbox';

        // foldable container
        var foldableContainer = createFoldableContainer(question, questionElement);

        // buttons
        var buttons = createToolboxButtons(question, questionElement);

        questionToolbox.appendChild(foldableContainer);
        questionToolbox.appendChild(buttons);

        return questionToolbox;
    }

    function createSctTypesSelect(questionNumber)
    {
        var sctTypesSelect = document.createElement('select');
        sctTypesSelect.id = 'sct_type_' + questionNumber;

        for (var i = 0; i < NewSctSubject.sctTypes.length; ++i)
        {
            var currentSctType =  NewSctSubject.sctTypes[i];

            var newOption = document.createElement('option');
            newOption.value = currentSctType.id;
            newOption.appendChild(document.createTextNode(currentSctType.name));

            sctTypesSelect.appendChild(newOption);
        }

        // on change listener
        sctTypesSelect.addEventListener('change', function(event) {
            var questionId = parseInt(event.target.id.replace('sct_type_', ''));
            NewSctSubject.questions[questionId - 1].typeId = event.target.value;
        });

        return sctTypesSelect;
    }

    function createSctTopicsListItems(topicNames)
    {
        return topicNames.map(function(name) {
            var newItem = document.createElement('li');
            newItem.appendChild(document.createTextNode(name));
            return newItem;
        });
    }

    function createSctTopicsList()
    {
        var sctTopicsList = document.createElement('ul');

        var topicsChildren = createSctTopicsListItems(['--']);

        for (var i = 0; i < topicsChildren.length; i++)
        {
            sctTopicsList.appendChild(topicsChildren[i]);
        }

        return sctTopicsList;
    }

    function createSctTopicsEditButton(questionNumber)
    {
        var editButton = createHoverableButton(undefined, _d('new_sct_subject', 'Edit'), 'var(--hover-button-default-border)', 'var(--hover-button-default-bg)');
        editButton.id = 'sct_topic_' + questionNumber;
        editButton.addEventListener('click', function (event) {

            // update the selected topics
            var questionNumber = parseInt(event.target.id.replace('sct_topic_', ''));
            NewSctSubject.currentSctQuestionNumber = questionNumber;
            var question = NewSctSubject.questions[questionNumber - 1];
            NewSctSubject.topicsSelection.update(question);

            // display the topics selection dialog box
            Dialog.appendDialogBox(_d('new_sct_subject', 'Edit SCT Topics'), _d('new_sct_subject', 'Choose the topics.'),
                NewSctSubject.subjectElement.topicsSelectionButtonHandlers, NewSctSubject.subjectElement.topicsSelection);
        });
        return editButton;
    }

    function createQuestionWording(questionNumber)
    {
        var wording = document.createElement('textarea');
        wording.id = 'sct_wording_' + questionNumber;
        wording.placeholder = _d('new_sct_subject', 'Describe the patient case...');
        wording.addEventListener('input', function(event) {
            var questionNumber = parseInt(event.target.id.replace('sct_wording_', ''));
            var question = NewSctSubject.questions[questionNumber - 1];
            question.wording = event.target.value;
        }, false);
        return wording;
    }

    function createQuestionElementTable(question, questionElement)
    {
        var questionElementTable = document.createElement("table");
        // ---------------------------------------------------------------------
        // SCT TYPES
        // sct types label
        var sctTypesLabel = document.createTextNode(_d('new_sct_subject', 'SCT Type:'));

        // sct types select
        var sctTypesSelect = createSctTypesSelect(question.id);

        // sct type row
        var sctTypeRow = NewSctSubject.createTableRow([undefined, sctTypesLabel, sctTypesSelect, undefined]);
        questionElementTable.appendChild(sctTypeRow);

        // ---------------------------------------------------------------------
        // SCT TOPICS
        // sct topics label
        var sctTopicsLabel = document.createTextNode(_d('new_sct_subject', 'SCT Topics:'));

        // sct topics list
        var sctTopicsList = createSctTopicsList();

        // sct topics edit button
        var sctTopicsEditButton = createSctTopicsEditButton(question.id);

        // sct topics row
        var sctTopicsRow = NewSctSubject.createTableRow([undefined, sctTopicsLabel, sctTopicsList, sctTopicsEditButton]);
        questionElementTable.appendChild(sctTopicsRow);

        // ---------------------------------------------------------------------
        // WORDING
        // wording label
        var wordingLabel = document.createTextNode(_d('new_sct_subject', 'Wording:'));

        // wording
        var wording = createQuestionWording(question.id);

        // wording row
        var wordingRow = NewSctSubject.createTableRow([undefined, wordingLabel, wording, undefined]);
        questionElementTable.appendChild(wordingRow);

        // ---------------------------------------------------------------------
        // QUESTION ELEMENTS CHILDREN
        questionElement.editTopicsButton    = sctTopicsEditButton;
        questionElement.typesSelect         = sctTypesSelect;
        questionElement.wording             = wording;
        questionElement.topicsList          = sctTopicsList;

        return questionElementTable;
    }

    function createQuestionNode(question, questionElement)
    {
        var questionNode = document.createElement('div');
        questionNode.className = "sct_question";

        questionNode.ondragover = function(event) {
            event.preventDefault();
            this.className = 'drag_destination';
            dragDestinationNumber = question.id;
        };
        questionNode.ondragend = function(event) {
            this.className = 'sct_question';
        };
        questionNode.ondragexit = questionNode.ondragend;
        questionNode.ondrop = function(event) {
            event.preventDefault();
            this.className = 'sct_question';

            // switch the target and the destination
            let destinationId = dragDestinationNumber;
            let sourceId = event.dataTransfer.getData('question_number');
            
            if (destinationId == sourceId)
            {
                return;
            }

            // remove the drag indication
            NewSctSubject.subjectElement.questionElements[destinationId - 1].node.className = '';

            // switch questions
            NewSctSubject.questions[destinationId - 1].id = sourceId;
            NewSctSubject.questions[sourceId - 1].id = destinationId;
            let temp = NewSctSubject.questions[destinationId - 1];
            NewSctSubject.questions[destinationId - 1] = NewSctSubject.questions[sourceId - 1];
            NewSctSubject.questions[sourceId - 1] = temp;

            // update ids
            NewSctSubject.subjectElement.questionElements[sourceId - 1].updateNumber(destinationId);
            NewSctSubject.subjectElement.questionElements[destinationId - 1].updateNumber(sourceId);

            // switch question elements
            temp = NewSctSubject.subjectElement.questionElements[destinationId - 1];
            NewSctSubject.subjectElement.questionElements[destinationId - 1] = NewSctSubject.subjectElement.questionElements[sourceId - 1];
            NewSctSubject.subjectElement.questionElements[sourceId - 1] = temp;

            // switch nodes
            let sourceNode = NewSctSubject.subjectElement.questionElements[sourceId - 1].node;
            let destinationNode = NewSctSubject.subjectElement.questionElements[destinationId - 1].node;

            if (sourceNode.nextSibling == undefined)
            {
                NewSctSubject.subjectElement.questionsParent.removeChild(sourceNode);
                NewSctSubject.subjectElement.questionsParent.insertBefore(sourceNode, destinationNode);
                NewSctSubject.subjectElement.questionsParent.removeChild(destinationNode);
                NewSctSubject.subjectElement.questionsParent.appendChild(destinationNode);
            }
            else
            {
                let temp = sourceNode.nextSibling;
                NewSctSubject.subjectElement.questionsParent.removeChild(sourceNode);
                NewSctSubject.subjectElement.questionsParent.insertBefore(sourceNode, destinationNode);
                NewSctSubject.subjectElement.questionsParent.removeChild(destinationNode);
                NewSctSubject.subjectElement.questionsParent.insertBefore(destinationNode, temp);
            }
        };

        // question toolbox
        var questionToolbox = createQuestionToolbox(question, questionElement);
        questionNode.appendChild(questionToolbox);

        // foldable content
        var foldableContent = document.createElement('div');
        foldableContent.className = 'foldable_content';

        // question table
        var questionElementTable = createQuestionElementTable(question, questionElement);
        foldableContent.appendChild(questionElementTable);

        // add items container
        var itemsContainer = document.createElement('div');
        itemsContainer.className = 'sct_items_container';
        foldableContent.appendChild(itemsContainer);
        questionElement.itemsContainer = itemsContainer;
        questionElement.foldHandler.targetNode = foldableContent;

        // add item button
        var addItemButton = createHoverableButton(undefined, _d('new_sct_subject', 'New Item'), 'gray', 'lightgray');
        addItemButton.id = 'add_item_' + question.id;
        foldableContent.appendChild(addItemButton);
        addItemButton.style.width = '40%';
        addItemButton.style.marginTop = '20px';
        addItemButton.style.marginLeft = 'auto';
        addItemButton.style.marginRight = 'auto';
        addItemButton.style.display = 'block';
        addItemButton.addEventListener('click', function(event) {
            var id = parseInt(event.target.id.replace('add_item_', ''));
            var questionElement = NewSctSubject.subjectElement.questionElements[id - 1];
            questionElement.addItem(new SctItem('', new SctData()));
        });
        questionElement.addItemButton = addItemButton;

        questionNode.appendChild(foldableContent);

        // add to the questions list
        NewSctSubject.subjectElement.questionsParent.appendChild(questionNode);

        // separator
        var separator = NewSctSubject.createSctSeparator();
        questionElement.separator = separator;
        NewSctSubject.subjectElement.questionsParent.appendChild(separator);

        return questionNode;
    }

    // -------------------------------------------------------------------------
    // DELETE QUESTION
    // -------------------------------------------------------------------------
    NewSctSubject.deleteSctQuestion = function (number)
    {
        var questionElements = NewSctSubject.subjectElement.questionElements;

        NewSctSubject.questions.splice(number - 1, 1);
        var questionElementToRemove = questionElements.splice(number - 1, 1)[0];
        NewSctSubject.subjectElement.questionsParent.removeChild(questionElementToRemove.separator);
        NewSctSubject.subjectElement.questionsParent.removeChild(questionElementToRemove.node);

        if (NewSctSubject.questions.length == 1)
        {
            questionElements[0].deleteButton.style.display = 'none';
        }

        // update all question after the deleted one
        for (var i = number - 1; i < questionElements.length; i++)
        {
            var element = questionElements[i];
            element.id--;
            var question = NewSctSubject.questions[i];
            question.id--;

            element.updateNumber(question.id);
        }

        NewSctSubject.updateSubjectStatus();

        if (NewSctSubject.questions.length < NewSctSubject.maxQuestions)
        {
            NewSctSubject.newQuestionButton.style.display = 'block';
            document.getElementById('new_question_separator').style.display = 'block';
            NewSctSubject.addQuestionsButton.style.display = 'inline-block';
        }
    }

    NewSctSubject.SctQuestionElement = class {
        constructor(question)
        {
            this.id = question.id;
            this.items = [];
            this.node = createQuestionNode(question, this);
        }

        // ---------------------------------------------------------------------
        // UPDATE QUESTION
        // ---------------------------------------------------------------------
        // refresh the question content
        updateNumber(newQuestionNumber)
        {
            this.id = newQuestionNumber;

            // question number
            this.numberField.innerHTML = Main.sprintf(_d('new_sct_subject', 'Question %1'), newQuestionNumber);

            // ids
            this.numberField.id         = 'question_number_' + newQuestionNumber;
            this.editTopicsButton.id    = "sct_topic_" + newQuestionNumber;
            this.typesSelect.id         = "sct_type_" + newQuestionNumber;
            this.wording.id             = "sct_wording_" + newQuestionNumber;
            this.addItemButton.id       = "add_item_" + newQuestionNumber;

            // item elements
            var question = NewSctSubject.questions[newQuestionNumber - 1];
            for (var i = 0; i < this.items.length; i++)
            {
                this.items[i].update(newQuestionNumber, i + 1, question.items[i]);
            }
        }

        updateTopics(question)
        {
             // clear the current list
            Main.removeAllChildren(this.topicsList);

            // add the new topics
            var noTopics = true;
            for (var i = 0; i < question.selectedTopics.length; i++)
            {
                if (question.selectedTopics[i])
                {
                    noTopics = false;
                    break;
                }
            }
            var topicsNames = ['--'];
            if (!noTopics)
            {
                topicsNames = [];
                for (var i = 0; i < question.selectedTopics.length; i++)
                {
                    if (question.selectedTopics[i])
                    {
                        topicsNames.push(NewSctSubject.sctTopics[i].name);
                    }
                }
            }
            var topicItems = createSctTopicsListItems(topicsNames);
            for (var i = 0; i < topicItems.length; i++)
            {
                this.topicsList.appendChild(topicItems[i]);
            }
        }

        updateWording(question)
        {
            this.wording.value = question.wording;
        }

        // ---------------------------------------------------------------------
        // ITEMS MANAGMENT
        // ---------------------------------------------------------------------
        addItem(newItem)
        {
            var items = NewSctSubject.questions[this.id - 1].items;
            items.push(newItem);

            var newItemElement = new NewSctSubject.SctItemElement(this.id, newItem, items.length);
            this.itemsContainer.appendChild(newItemElement.node);
            this.items.push(newItemElement);

            NewSctSubject.updateSubjectStatus();
        }

        removeItem(itemNumber)
        {
            // update model
            NewSctSubject.questions[this.id - 1].items.splice(itemNumber - 1, 1);

            // update tree
            var itemToRemove = this.items.splice([itemNumber - 1], 1)[0];
            this.itemsContainer.removeChild(itemToRemove.node);

            for (var i = itemNumber - 1; i < this.items.length; ++i)
            {
                this.items[i].update(this.id, i + 1);
            }

            // hide delete button
            if (this.items.length < 2)
            {
                this.items[0].deleteButton.style.display = 'none';
            }

            NewSctSubject.updateSubjectStatus();
        }
    }

    let dragDestinationNumber = -1;
})(window.NewSctSubject = window.NewSctSubject || {});