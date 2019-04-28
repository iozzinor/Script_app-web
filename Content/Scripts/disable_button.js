function createDisableButton(existingButton, backgroundColor, borderColor, textColor)
{
    let button = existingButton || button;

    button.previousBackgroundColor  = button.style.backgroundColor;
    button.previousBorderColor      = button.style.borderColor;
    button.previousTextColor        = button.style.color;

    button.setDisabled = function (disabled) {
        this.disabled = disabled;
        if (disabled)
        {
            this.style.backgroundColor  = backgroundColor;
            this.style.borderColor      = borderColor;
            this.style.color            = textColor;
        }
        // restore values
        else
        {
            this.style.backgroundColor  = this.previousBackgroundColor;
            this.style.borderColor      = this.previousBorderColor;
            this.style.color            = '';
        }
    };

    return button;
}