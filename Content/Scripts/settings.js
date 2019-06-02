(function(Settings) {
    function setupLanguages()
    {
        let languageOption = document.getElementById('language');
        for (var i = 0; i < Settings.sctLanguages.length; ++i)
        {
            let currentLanguage = Settings.sctLanguages[i];

            let newOption = document.createElement('option');
            newOption.appendChild(document.createTextNode(currentLanguage.shortName + ' - ' + currentLanguage.name));

            languageOption.appendChild(newOption);
        }
    }

    setupLanguages()
})(window.Settings = window.Settings || {});