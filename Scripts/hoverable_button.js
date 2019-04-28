function createHoverableButton(existingButton, title, borderColor, backgroundColor)
{
    var button = existingButton || document.createElement('input');
    button.type = 'button';
    button.value = title;
    button.style.backgroundColor = backgroundColor;
    button.style.borderColor = borderColor;
    button.className = 'hoverable_button'

    button.addEventListener('mouseover', function(event) {
        if (!event.target.disabled)
        {
            event.target.style.backgroundColor = borderColor;
        }
    });
    button.addEventListener('mouseout', function(event) {
        if (!event.target.disabled)
        {
            event.target.style.backgroundColor = backgroundColor;
        }
    });

    return button;
}