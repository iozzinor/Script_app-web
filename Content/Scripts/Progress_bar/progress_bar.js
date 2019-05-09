(function(ProgressBar) {
    ProgressBar.Bar = class {
        constructor(progressColor, remainingColor)
        {
            this.progressColor  = progressColor;
            this.remainingColor = remainingColor;

            this.container              = document.createElement('div');
            this.container.className    = 'progress_bar_container';

            this.label              = document.createElement('p');
            this.label.className    = 'progress_bar_label';

            this.node = document.createElement('canvas');
            this.node.className = 'progress_bar';
			this.context = this.node.getContext('2d');
			this.context.imageSmoothingEnabled = false;

            // hierarchy
            this.container.appendChild(this.label);
            this.container.appendChild(this.node);

			// set the progression to 0
			this.setProgress(0);
        }

		setProgress(progress)
		{
			progress = Math.min(Math.max(progress, 0), 100);
			this.label.innerHTML = progress + '%';

			this.drawBar(progress);
		}

		drawBar(progress)
		{
			this.displayProgress(progress);
			this.displayRemaining(progress);
		}

		displayProgress(progress)
		{
			this.context.fillStyle = this.progressColor;
			let x = Math.floor(progress / 100.0 * this.node.width);
			this.context.fillRect(0, 0, x, this.node.height);
		}

		displayRemaining(progress)
		{
			this.context.fillStyle = this.remainingColor;
			let x 		= Math.ceil(progress / 100.0 * this.node.width);
			let width 	= this.node.width - x;
			this.context.fillRect(x, 0, width, this.node.height);
		}
    };

})(window.ProgressBar = window.ProgressBar || {});
