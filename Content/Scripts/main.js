(function(Main) {
    // -------------------------------------------------------------------------
    // DOM TREE MANIPULATION
    // -------------------------------------------------------------------------
    Main.removeAllChildren = function (element) {
        for (var i = element.children.length - 1; i > -1; --i)
        {
            element.removeChild(element.children[i]);
        }
    };

    Main.appendChildren = function(element, children) {
        for (var i = 0; i < children.length; ++i)
        {
            element.appendChild(children[i]);
        }
    }
    
    // -------------------------------------------------------------------------
    // DOM POSITION
    // -------------------------------------------------------------------------
    Main.getOffset = function (element) {
        if (!element)
        {
            return undefined;
        }

        var x = 0;
        var y = 0;
        while (element && !isNaN(element.offsetLeft) && !isNaN(element.offsetTop))
        {
            x += element.offsetLeft - element.scrollLeft;
            y += element.offsetTop - element.scrollTop;
            element = element.offsetParent;
        }

        return { left: x, top: y};
    };

    // -------------------------------------------------------------------------
    // STRING
    // -------------------------------------------------------------------------
    Main.sprintf = function(format, arguments) {
        // format is empty
        if (format == null || typeof format == undefined || format == '')
        {
            return '';
        }
        // no arguments to insert
        let percentPosition = format.indexOf('%');
        if (percentPosition == -1)
        {
            return format;
        }

        // make sure that arguments in an array
        if (arguments == undefined || arguments == null)
        {
            arguments = [];
        }
        else if (arguments.constructor != Array)
        {
            arguments = [arguments];
        }

        // find % characters in the string
        let previousPosition = 0;
        percentPosition = 0;
        let result = '';
        let digitMatch;
        while (percentPosition < format.length && (percentPosition = format.indexOf('%', percentPosition)) != -1)
        {
            // append the string
            result += format.substring(previousPosition, percentPosition);

            // percent symbol
            if (format.substring(percentPosition, percentPosition + 2) == '%%')
            {
                result += '%';
                percentPosition += 2;
            }
            // argument number
            else if (digitMatch = format.substring(percentPosition).match(/^%(\d+)/))
            {
                // get the argument position
                let argumentPosition = parseInt(digitMatch[1]);

                // get the argument
                if (argumentPosition > 0 && argumentPosition < arguments.length + 1)
                {
                    let argument = arguments[argumentPosition - 1];
                    result += argument;
                }

                percentPosition += 1 + argumentPosition.toString().length;
            }
            // nothing in perticular, append the rest of the string
            else
            {
                result += format.substring(percentPosition + 1);
                percentPosition = format.length;
            }
            previousPosition = percentPosition;
        }
        if (previousPosition < format.length)
        {
            result += format.substring(previousPosition );
        }

        return result;
    };
})(window.Main = window.Main || {});

// -----------------------------------------------------------------------------
// SETUP TRANSLATION FACILITIES
// -----------------------------------------------------------------------------
Gettext.bindFunctions();
Gettext.tryAutomaticLoading();
Gettext.textdomain('common');