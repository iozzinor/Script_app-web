(function(Gettext) {
    Gettext.en_US = {
        pluralRules: [
            n => n == 1 // singular
        ],

        messages: {
            singulars: {
                hello: "Say hello when you meet someone !"
            },

            plurals: {
                bug: ["There is %d bug in my program.", "There are %d bugs in my program"]
            }
        }
    };
})(window.Gettext = window.Gettext || {});