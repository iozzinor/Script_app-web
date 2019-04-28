(function(Dialog) {
    // -------------------------------------------------------------------------
    // NODES CREATION
    // -------------------------------------------------------------------------
    function createDialogBackground()
    {
        let dialogBackground = document.createElement('div');
        dialogBackground.className = "dialog_background";
        dialogBackground.style.display = '';
        document.body.appendChild(dialogBackground);

        return dialogBackground;
    }

    function createButtonsContainer()
    {
        var buttonsContainer = document.createElement('div');
        buttonsContainer.className = 'dialog_buttons_container';

        return buttonsContainer;
    }

    function center(element)
    {
        var x = (window.innerWidth - element.clientWidth) / 2.0;
        var y = (window.innerHeight - element.clientHeight) / 2.0;

        element.style.width     = '';
        element.style.height    = '';
        element.style.overflowX = 'none';
        element.style.overflowY = 'none';
        if (x < 0)
        {
            element.style.overflowX = 'scroll';
            x = 5;
            element.style.width = (window.innerWidth - 10) + "px";
        }
        if (y < 0)
        {
            element.style.overflowY = 'scroll';
            y = 5;
            element.style.height = (window.innerHeight - 10) + "px";
        }
        element.style.left = x + "px";
        element.style.top = y + "px";
    }

    // -------------------------------------------------------------------------
    // FOCUS
    // -------------------------------------------------------------------------
    function allowFocus(element)
    {
        let topDialogBox = boxes[boxes.length - 1].node;
        if (element == topDialogBox)
        {
            return true;
        }
        
        var parent = element.parentNode;
        while (parent != undefined)
        {
            if (parent == topDialogBox)
            {
                return true;
            }
            parent = parent.parentNode;
        }
        return false;
    }

    window.addEventListener('focusin', function(event) {
        if (boxes.length < 1)
        {
            return;
        }

        if (!allowFocus(event.target))
        {
            focusElement.focus();
        }
        else
        {
            focusElement = event.target;
        }
    });

    // -------------------------------------------------------------------------
    // DIALOG BOX
    // -------------------------------------------------------------------------
    DialogBox = class {
        constructor(title, message, buttonHandlers, content)
        {
            if (Dialog.background == undefined)
            {
                Dialog.background = createDialogBackground();
            }
            Dialog.background.style.display = '';

            this.node = document.createElement('div');
            this.node.className = "dialog_box";

            // title
            if (title)
            {
                let titleNode = document.createElement('h1');
                titleNode.appendChild(document.createTextNode(title));
                this.node.appendChild(titleNode);
            }

            // message
            if (message)
            {
                let messageNode = document.createElement('p');
                messageNode.appendChild(document.createTextNode(message));
                this.node.appendChild(messageNode);
            }

            // content
            if (content)
            {
                this.node.appendChild(content);
            }

            // buttons container
            let buttonsContainer = createButtonsContainer();
            let buttonWidth = parseInt(90.0 / buttonHandlers.length) + "%";
            for (var i = 0; i < buttonHandlers.length; ++i)
            {
                let currentButtonHandler = buttonHandlers[i] || defaultHandler;
                let newButton = currentButtonHandler.createButton() || defaultHandler.createButton();
                newButton.style.width = buttonWidth;
                newButton.addEventListener('click', function(event) {
                    let shouldCloseDialog = true;
                    if (currentButtonHandler.buttonClicked)
                    {
                        shouldCloseDialog = Boolean(currentButtonHandler.buttonClicked(event));
                    }

                    if (shouldCloseDialog)
                    {
                        closeTopDialog();
                    }
                });
                buttonsContainer.appendChild(newButton);

                if (i == 0)
                {
                    focusElement = newButton;
                    newButton.focus();
                }
            }
            this.node.appendChild(buttonsContainer);

            document.body.appendChild(this.node);
            center(this.node);
        }
    };

    Dialog.ButtonHandler = class  {
        // createButton: function that return the new button
        // buttonClicked: function trigger when the button is clicked
        //  and that should return whether the dialog should be closed
        //  If it is undefined, then the dialog will be closed.
        constructor(createButton, buttonClicked)
        {
            this.createButton = createButton;
            this.buttonClicked = buttonClicked;
        }
    };

    // -------------------------------------------------------------------------
    // CLOSE DIALOG
    // -------------------------------------------------------------------------
    function closeTopDialog()
    {
        let topDialog = boxes[boxes.length - 1];
        document.body.removeChild(topDialog.node)
        boxes.splice(boxes.length - 1);

        if (boxes.length < 1)
        {
            Dialog.background.style.display = 'none';
        }
    }

    // -------------------------------------------------------------------------
    // APPEND DIALOG
    // -------------------------------------------------------------------------
    Dialog.appendDialogBox = function(title, message, buttonHandlers, content) {

        if (buttonHandlers == undefined || buttonHandlers.length < 1)
        {
            buttonHandlers = [defaultHandler];
        }

        var newDialogBox = new DialogBox(title, message, buttonHandlers, content);

        boxes.push(newDialogBox);
    };

    let boxes = [];
    let focusElement = {};
    let defaultHandler = new Dialog.ButtonHandler(function() {
        var button = document.createElement('input');
        button.type = 'button';
        button.value = 'Ok';
        return button;
    }, function() { return true; });

    // -------------------------------------------------------------------------
    // WINDOW RESIZE
    // -------------------------------------------------------------------------
    window.addEventListener('resize', function (event) {
        for (var i = 0; i < boxes.length; ++i)
        {
            var dialogBox = boxes[i].node;
            center(dialogBox);
        }
    });
}) (window.Dialog = window.Dialog || {});