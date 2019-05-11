(function(NewSctSubject) {
    // ---------------------------------------------------------------------
    // UTILS
    // ---------------------------------------------------------------------
    NewSctSubject.createTableRow = function (children, cellType) {
        var cellType = cellType || "td";

        var newRow = document.createElement('tr');

        for (var i = 0; i < children.length; i++)
        {
            var child = children[i];
            var newCell = document.createElement(cellType);
            if (child != undefined)
            {
                newCell.appendChild(child);
            }
            newRow.appendChild(newCell);
        }

        return newRow;
    }

    // ---------------------------------------------------------------------
    // ID
    // ---------------------------------------------------------------------
    function createId(name, itemIdentification)
    {
        return 'sct_item_' + name + '_' + itemIdentification.question + '_' + itemIdentification.item;
    }

    let idRegex = new RegExp(/.*[_](\d+)[_](\d+)/);

    function extractNumber(id)
    {
        var result = idRegex.exec(id);
        if (!result || result.length < 3)
        {
            return null;
        }
        return {
            question: result[1],
            item: result[2]
        }
    }

    // ---------------------------------------------------------------------
    // CLASS DATA HANDLER
    // ---------------------------------------------------------------------
    class DataHandler
    {
        // associatedDataOnEvent is called with the event and should return
        // the new associated data
        //
        // retrieveAssociatedData is called when the data type is changed,
        // with the node as the arguent and should return the associated data
        constructor(node, idName, eventName, associatedDataOnEvent, retrieveAssociatedData)
        {
            this.idName     = idName;
            this.node       = node;
            this.retrieveAssociatedData = retrieveAssociatedData;

            this.node.addEventListener(eventName, function(event) {
                var associatedData = associatedDataOnEvent(event);

                var itemIdentification = extractNumber(event.target.id);

                // update the sct item
                var question = NewSctSubject.questions[itemIdentification.question - 1];
                var item = question.items[itemIdentification.item - 1];

                var questionElement = NewSctSubject.subjectElement.questionElements[itemIdentification.question - 1];
                var itemElement = questionElement.items[itemIdentification.item - 1];

                item.newData.associatedData = associatedData;
                item.newData.dataType = itemElement.currentDataType;
            });
        }

        updateId(itemInformation)
        {
            this.node.id = createId('data_' + this.idName, itemInformation);
            this.node.name = this.node.id;
        }

        updateItem(dataType)
        {
            var itemIdentification = extractNumber(event.target.id);

            // update the sct item
            var question = NewSctSubject.questions[itemIdentification.question - 1];
            var item = question.items[itemIdentification.item - 1];
            
            item.newData.associatedData = this.retrieveAssociatedData(this.node);
            item.newData.dataType       = dataType;
        }
    }

    // ---------------------------------------------------------------------
    // NEW ITEM
    // ---------------------------------------------------------------------
    function createDeleteButton(questionNumber, itemNumber)
    {
        var deleteButton = createHoverableButton(undefined, _d('new_sct_subject', 'Delete'), 'var(--hover-button-delete-border)', 'var(--hover-button-delete-bg)');
        deleteButton.className += " delete_button";

        if (itemNumber < 2)
        {
            deleteButton.style.display = 'none';
        }
        else
        {
            var itemElement = NewSctSubject.subjectElement.questionElements[questionNumber - 1].items[0];
            itemElement.deleteButton.style.display = '';
        }

        deleteButton.addEventListener('click', function(event) {
            var itemIdentification = extractNumber(event.target.id);
            var question = NewSctSubject.subjectElement.questionElements[itemIdentification.question - 1];
            question.removeItem(itemIdentification.item);
        });

        return deleteButton;
    }

    function createToolbox(itemElement, questionNumber, itemNumber)
    {
        var itemToolbox = document.createElement('div');
        itemToolbox.className = 'sct_item_toolbox';

        var deleteButton = createDeleteButton(questionNumber, itemNumber);
        var itemNumberElement = document.createElement('p');
        itemNumberElement.appendChild(document.createTextNode(Main.sprintf(_d('new_sct_subject', 'Item %1'), itemNumber)));

        var itemButtonsToolbox = createButtonsToolbox([deleteButton]);

        itemToolbox.appendChild(itemNumberElement);
        itemToolbox.appendChild(itemButtonsToolbox);

        itemElement.deleteButton = deleteButton;
        itemElement.numberField  = itemNumberElement;

        return itemToolbox;
    }

    function createButtonsToolbox(buttons)
    {
        var buttonsToolbox = document.createElement('div');
        buttonsToolbox.className = 'sct_item_toolbox_buttons';

        for (var i = 0; i < buttons.length; ++i)
        {
            buttonsToolbox.appendChild(buttons[i]);
        }
        return buttonsToolbox;
    }

    function createHypothesisField()
    {
        var hypothesisField = document.createElement('textarea');

        hypothesisField.placeholder = _d('new_sct_subject', 'The Hypothesis...');
        hypothesisField.addEventListener('input', function (event) {
            var itemIdentification = extractNumber(event.target.id);
            var question = NewSctSubject.questions[itemIdentification.question - 1];
            question.items[itemIdentification.item - 1].hypothesis = event.target.value;
        });

        return hypothesisField;
    }

    function createItemDataTypeSelect()
    {
        var dataTypesSelect = document.createElement('select');

        for (var i = 0; i < SctDataType.types.length; i++)
        {
            var dataTypeName = SctDataType.types[i].name;

            var newOption = document.createElement('option');
            newOption.value = i;
            newOption.append(document.createTextNode(dataTypeName));

            dataTypesSelect.appendChild(newOption);
        }

        dataTypesSelect.addEventListener('change', function (event) {
            var itemIdentification = extractNumber(event.target.id);
            var question = NewSctSubject.subjectElement.questionElements[itemIdentification.question - 1];
            var item = question.items[itemIdentification.item - 1];

            let dataType = SctDataType.types[event.target.value];
            item.setDataType(dataType);
        });

        return dataTypesSelect;
    }

    function createDataText()
    {
        var dataText = document.createElement('input');
        dataText.placeholder = _d('new_sct_subject', _d('new_sct_subject', 'The New Data...'));
        return dataText;
    }

    function createDataFile(acceptedTypes)
    {
        var dataFile = document.createElement('input');
        dataFile.type = 'file';

        dataFile.accept = '';
        for (var i = 0; i < acceptedTypes.length; ++i)
        {
            dataFile.accept += acceptedTypes[i];
            if (i < acceptedTypes.length - 1)
            {
                dataFile.accept += ',';
            }
        }

        return dataFile;
    }

    function createDataHandlers()
    {
        let onEventInputValueGetter = (event => event.target.value);
        let inputValueGetter = (node => node.value);
        return  {
            text:   new DataHandler(createDataText(), 'text', 'input', onEventInputValueGetter, inputValueGetter),
            image:  new DataHandler(createDataFile(['image/*']), 'image', 'change', onEventInputValueGetter, inputValueGetter),
            volume: new DataHandler(createDataFile(['.stl']), 'volume', 'change', onEventInputValueGetter, inputValueGetter)
        };
    }

    function createItemNode(itemElement, questionNumber, itemNumber)
    {
        let itemNode = document.createElement('div');
        itemNode.id = "sct_item_" + questionNumber + '_' + itemNumber;
        itemNode.className = "sct_item";

        var tableItemNode = document.createElement('table');

        // item number
        var toolbox = createToolbox(itemElement, questionNumber, itemNumber);

        // hypothesis
        var hypothesisField = createHypothesisField();
        var hypothesisRow = NewSctSubject.createTableRow([undefined, document.createTextNode(_d('new_sct_subject', 'Hypothesis:')), hypothesisField]);

        // data type
        var dataTypeSelect = createItemDataTypeSelect();
        var dataTypeRow = NewSctSubject.createTableRow([undefined, document.createTextNode(_d('new_sct_subject', 'New Data Type:')), dataTypeSelect]);

        // data
        var dataRow = NewSctSubject.createTableRow([undefined, document.createTextNode(_d('new_sct_subject', 'New Data:')), undefined]);

        tableItemNode.appendChild(hypothesisRow);
        tableItemNode.appendChild(dataTypeRow);
        tableItemNode.appendChild(dataRow);

        itemNode.appendChild(toolbox);
        itemNode.appendChild(tableItemNode);

        // add elements to the main element
        itemElement.hypothesisField     = hypothesisField;
        itemElement.dataTypeSelect      = dataTypeSelect;
        itemElement.dataParent          = dataRow.children[2];
        
        // set the current data handler to text handler
        itemElement.dataHandlers = createDataHandlers();
        var textHandler = itemElement.dataHandlers.text;
        itemElement.currentDataType = SctDataType.types[0];
        itemElement.currentDataTypeName = itemElement.currentDataType.nameCode.toLowerCase();
        itemElement.dataParent.appendChild(textHandler.node);
        itemElement.currentDataHandler = textHandler;

        return itemNode;
    }

    NewSctSubject.SctItemElement = class {
        constructor(questionId, item, itemNumber)
        {
            this.questionNumber = questionId;
            this.itemNumber = itemNumber;
            this.node = createItemNode(this, questionId, itemNumber);
            this.setIds(questionId, itemNumber);
        }

        // -----------------------------------------------------------------
        // UPDATE
        // -----------------------------------------------------------------
        update(questionId, itemNumber, item)
        {
            if (questionId != undefined)
            {
                this.questionNumber = questionId;
            }
            if (itemNumber != undefined)
            {
                this.itemNumber = itemNumber;

                this.numberField.textContent = 'Item ' + itemNumber;
            }
            if (item != undefined)
            {
                this.hypothesisField.value = item.hypothesis;
            }

            // ids
            this.setIds(this.questionNumber, this.itemNumber);
        }

        setIds(questionNumber, itemNumber)
        {
            let itemIdentification = {
                question: questionNumber,
                item: itemNumber
            };
            this.node.id = 'sct_item_' + questionNumber + '_' + itemNumber;
            this.deleteButton.id     = createId('delete', itemIdentification);
            this.hypothesisField.id  = createId('hypothesis', itemIdentification);
            this.dataTypeSelect.id   = createId('type_select', itemIdentification);
            
            for (var i = 0; i < SctDataType.types.length; i++)
            {
                var currentType = SctDataType.types[i];

                if (currentType.nameCode.toLowerCase() in this.dataHandlers)
                {
                    this.dataHandlers[currentType.nameCode.toLowerCase()].updateId(itemIdentification);
                }
            }

            // names
            this.hypothesisField.name   = this.hypothesisField.id;
            this.dataTypeSelect.name    = this.dataTypeSelect.id;
        }

        setDataType(dataType)
        {
            if (dataType.nameCode == this.currentDataTypeName)
            {
                return;
            }
            this.currentDataTypeName = dataType.nameCode;
            this.currentDataType = dataType;

            // the data type is found in the data informaiton array
            if (dataType.nameCode.toLowerCase() in this.dataHandlers)
            {
                this.updateDataType(dataType);
            }
        }

        updateDataType(dataType)
        {
            var newDataHandler = this.dataHandlers[dataType.nameCode.toLowerCase()];

            if (this.currentDataHandler != newDataHandler)
            {
                // update data child
                if (this.currentDataHandler.node != newDataHandler.node)
                {
                    this.dataParent.removeChild(this.currentDataHandler.node);
                    this.dataParent.appendChild(newDataHandler.node);
                }
                this.currentDataHandler = newDataHandler;

                // update item
                this.currentDataHandler.updateItem(dataType);
            }
        }
    };

})(window.NewSctSubject = window.NewSctSubject || {});