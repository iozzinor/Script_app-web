class SctData
{
    constructor(dataType, associatedData)
    {
        this.dataType = dataType || SctDataType.types[0];
        this.associatedData = associatedData || '';
    }
}