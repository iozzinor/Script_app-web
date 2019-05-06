var nameField = document.getElementById('new_sct_type_field');

function validateSctTypeName()
{
    var newTypeName = nameField.value;
    if (newTypeName == '')
    {
        alert('Please enter a new type name!');
    }
    return newTypeName != '';
}

function deleteSctTypeName(name)
{
    if (window.location.href.indexOf('delete_type_name') > -1)
    {
        window.location.href = window.location.href.replace(/(delete_type_name=)[^&]+/g, "$1" + name);
    }
    else
    {
        window.location.href += '?delete_type_name=' + name;
    }
}