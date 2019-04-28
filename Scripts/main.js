(function(Main) {
    // -------------------------------------------------------------------------
    // UTILS
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
})(window.Main = window.Main || {});