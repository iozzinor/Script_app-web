(function(Gettext) {
    Gettext._ = function(messageId) {
        return Gettext._d(currentDomain, messageId);
    };

    Gettext._d = function(domain, messageId) {
        if (
            !currentLanguage ||
            !(currentLanguage in languagesData) ||
            !(domain in languagesData[currentLanguage]) ||
            !('singulars' in languagesData[currentLanguage][domain])
        )
        {
            return messageId;
        }
        return languagesData[currentLanguage][domain].singulars[messageId] || messageId;
    };

    Gettext._n = function(singularMessageId, pluralMessageId, count) {
        return Gettext._dn(currentDomain, singularMessageId, pluralMessageId, count);
    };

    Gettext._dn = function(domain, singularMessageId, pluralMessageId, count) {
        if (
            !currentLanguage ||
            !(currentLanguage in languagesData) ||
            !(domain in languagesData[currentLanguage]) ||
            !('pluralRules' in languagesData[currentLanguage]) ||
            !('plurals' in languagesData[currentLanguage][domain]) ||
            !(singularMessageId in languagesData[currentLanguage][domain].plurals)
        )
        {
            return count == 1 ? singularMessageId : pluralMessageId;
        }

        // get the rule to apply
        let rules = languagesData[currentLanguage]['pluralRules'];
        var ruleIndex = rules.length;
        for (var i = 0; i < rules.length; i++)
        {
            if (rules[i](count))
            {
                ruleIndex = i;
                break;
            }
        }

        return languagesData[currentLanguage][domain].plurals[singularMessageId][ruleIndex] || singularMessageId;
    };

    Gettext.textdomain = function(domain) {
        if (languagesData[currentLanguage] === undefined)
        {
            return null;
        }

        if (!(domain in languagesData[currentLanguage]))
        {
            return null;
        }
        currentDomain = domain;
        return currentDomain;
    };

    Gettext.getCurrentDomain = function() {
        return currentDomain;
    };

    Gettext.setlocale = function(newLanguage) {
        currentLanguage = newLanguage;
        return currentLanguage;
    };

    Gettext.getCurrentLanguage = function () {
        return currentLanguage;
    };

    let languagesData   = {};
    let currentDomain   = 'messages';
    let currentLanguage = undefined;

    Gettext.bindFunctions = function() {
        // export convenience functions
        window._    = window._      || Gettext._;
        window._d   = window._d     || Gettext._d;
        window._n   = window._n     || Gettext._n;
        window._dn  = window._dn    || Gettext._dn;
    };

    Gettext.tryAutomaticLoading = function() {
        for (var key in Gettext)
        {
            // assert the property is an object
            var potentialLanguageData = Gettext[key];
            if (typeof potentialLanguageData !== 'object')
            {
                continue;
            }

            // check for plural rules
            if (!('pluralRules' in potentialLanguageData))
            {
                console.warn('Loaded language data for which no plural rules were defined (language locale: ' + key + ').');
            }
            
            languagesData[key] = potentialLanguageData;

            if (currentLanguage == undefined)
            {
                initializeDomain(key, potentialLanguageData);
            }
        }
    };

    function initializeDomain(languageName, domainData)
    {
        currentLanguage = languageName;

        for (var key in domainData)
        {
            if (key === 'pluralRules')
            {
                continue;
            }
            var potentialDomain = domainData[key];
            if (typeof potentialDomain === 'object')
            {
                currentDomain = key;
                break;
            }
        }
    }
    
})(window.Gettext = window.Gettext || {});