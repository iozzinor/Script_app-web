(function(ProgressBar) {
    ProgressBar.Bar = class {
        constructor(clearColor, progressColor, remainingColor)
        {
            this.clearColor     = clearColor;
            this.progressColor  = progressColor;
            this.remainingColor = remainingColor;

            this.container              = document.createElement('div');
            this.container.className    = 'progress_bar_container';

            this.label              = document.createElement('p');
            this.label.className    = 'progress_bar_label';

            this.node = document.createElement('canvas');
            this.node.class = 'progress_bar';

            // hierarchy
            this.container.appendChild(this.progressBar);
            this.container.appendChild(this.node);
        }
    }; 
})(window.ProgressBar = window.ProgressBar || {});