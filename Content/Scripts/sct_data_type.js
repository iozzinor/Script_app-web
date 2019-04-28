class SctDataType
{
    constructor(name)
    {
        if (name in SctDataType.names)
        {
            console.error('Name ' + name + ' already used => override the SCT data type.');
        }
        else
        {
            SctDataType.names.push(name);
        }
        this.name = name;
    }
}

SctDataType.names = [];

SctDataType.types = [
    new SctDataType('Text'),
    new SctDataType('Image'),
    new SctDataType('Volume')
];

