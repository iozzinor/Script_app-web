var nameField = document.getElementById('new_sct_topic_field');

function validateSctTopicName()
{
    var newTopicName = nameField.value;
    if (newTopicName == '')
    {
        alert('Please enter a new topic name!');
    }
    return newTopicName != '';
}

function deleteSctTopicName(name)
{
    if (window.location.href.indexOf('delete_topic_name') > -1)
    {
        window.location.href = window.location.href.replace(/(delete_topic_name=)[^&]+/g, "$1" + name);
    }
    else
    {
        window.location.href += '?delete_topic_name=' + name;
    }
}