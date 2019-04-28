// -------------------------------------------------------------------------
// SCT QUESTION
// -------------------------------------------------------------------------
class SctQuestion
{
    constructor(id, itemsCount)
    {
        this.id = id;
        this.typeId = NewSctSubject.sctTypes[0].id;
        this.selectedTopics = Array.apply(null, Array(NewSctSubject.sctTopics.length).map(function() { return false; }));
        this.wording = "";

        itemsCount = itemsCount || 1;
        this.items = [];
    }
}