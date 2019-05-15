(function(Banner) {
    // -------------------------------------------------------------------------
    // TOP BANNER
    // -------------------------------------------------------------------------
    class TopBanner
    {
        constructor(title, message, buttons, clickHandler)
        {
            title           = title || '';
            message         = message || '';
            buttons         = buttons || [];
            if (buttons.length < 1)
            {
                buttons = [defaultButton];
            }
            clickHandler    = clickHandler || defaultClickHandler;

            this.node = document.createElement('div');
            this.node.className = 'top_banner';

            let titleNode = document.createElement('h1');
            titleNode.appendChild(document.createTextNode(title));

            let messageNode = document.createElement('p');
            messageNode.appendChild(document.createTextNode(message));

            let buttonsContainer = document.createElement('div');
            buttonsContainer.className = 'buttons_container';
            for (var i = 0; i < buttons.length; ++i)
            {
                buttons[i].addEventListener('click', function(event) {
                    if (clickHandler(event.target))
                    {
                        topBannerHeight = topBanner.node.clientHeight;
                        closeTopBannerAnimation();
                    }
                });

                buttonsContainer.appendChild(buttons[i]);
            }

            // hierarchy
            this.node.appendChild(titleNode);
            this.node.appendChild(messageNode);
            this.node.appendChild(buttonsContainer);

            let header = document.querySelector('header');
            document.body.insertBefore(this.node, header);
        }
    };

    // -------------------------------------------------------------------------
    // CLICK HANDLER
    // -------------------------------------------------------------------------
    // return wether the banner should be closed
    function defaultClickHandler(buttonClicked)
    {
        return true;
    }

    // -------------------------------------------------------------------------
    // OK BUTTON
    // -------------------------------------------------------------------------
    let defaultButton = document.createElement('button');
    defaultButton.innerHTML = 'Ok';

    // -------------------------------------------------------------------------
    // TOP BANNER
    // -------------------------------------------------------------------------
    function closeTopBannerAnimation()
    {
        if (topBanner == null)
        {
            return;
        }
        yPosition = yPosition || 0;
        yPosition -= 5;

        topBanner.node.style.top = yPosition + "px";

        if (yPosition > -topBannerHeight)
        {
            setTimeout(() => {
                closeTopBannerAnimation();
            }, 5);
        }
        else
        {
            removeTopBanner();
        }
    }

    function removeTopBanner()
    {
        document.body.removeChild(topBanner.node);

        topBanner = null;
        topBannerHeight = null;
        yPosition = null;
    }

    // only one top banner might be shown at a time
    let topBanner = null;
    let topBannerHeight = null;
    let yPosition = null;

    // -------------------------------------------------------------------------
    // APPEND BANNER
    // -------------------------------------------------------------------------
    Banner.appendTopBanner = function(title, message, buttons, clickHandler) {
        if (topBanner == null)
        {
            topBanner = new TopBanner(title, message, buttons, clickHandler);
        }
    };
})(window.Banner = window.Banner || {});