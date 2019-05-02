class SctDataType
{
    constructor(nameCode, name)
    {
        if (nameCode in SctDataType.nameCodes)
        {
            console.error('Name code ' + nameCode + ' already used => override the SCT data type.');
        }
        else
        {
            SctDataType.nameCodes.push(nameCode);
        }
        this.nameCode = nameCode;
        this.name = name;
    }
}

SctDataType.nameCodes = [];

SctDataType.types = [
    new SctDataType('Text', _d('new_sct_subject', 'data_type_text')),
    new SctDataType('Image', _d('new_sct_subject', 'data_type_image')),
    new SctDataType('Volume', _d('new_sct_subject', 'data_type_volume'))
];

