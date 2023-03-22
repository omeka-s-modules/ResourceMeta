$(function() {

const dctermsMap = new Map([
    ['dcterms:abstract', 'dcterms.abstract'],
    ['dcterms:accessRights', 'dcterms.accessRights'],
    ['dcterms:accrualMethod', 'dcterms.accrualMethod'],
    ['dcterms:accrualPeriodicity', 'dcterms.accrualPeriodicity'],
    ['dcterms:accrualPolicy', 'dcterms.accrualPolicy'],
    ['dcterms:alternative', 'dcterms.alternative'],
    ['dcterms:audience', 'dcterms.audience'],
    ['dcterms:available', 'dcterms.available'],
    ['dcterms:bibliographicCitation', 'dcterms.bibliographicCitation'],
    ['dcterms:conformsTo', 'dcterms.conformsTo'],
    ['dcterms:contributor', 'dcterms.contributor'],
    ['dcterms:coverage', 'dcterms.coverage'],
    ['dcterms:created', 'dcterms.created'],
    ['dcterms:creator', 'dcterms.creator'],
    ['dcterms:date', 'dcterms.date'],
    ['dcterms:dateAccepted', 'dcterms.dateAccepted'],
    ['dcterms:dateCopyrighted', 'dcterms.dateCopyrighted'],
    ['dcterms:dateSubmitted', 'dcterms.dateSubmitted'],
    ['dcterms:description', 'dcterms.description'],
    ['dcterms:educationLevel', 'dcterms.educationLevel'],
    ['dcterms:extent', 'dcterms.extent'],
    ['dcterms:format', 'dcterms.format'],
    ['dcterms:hasFormat', 'dcterms.hasFormat'],
    ['dcterms:hasPart', 'dcterms.hasPart'],
    ['dcterms:hasVersion', 'dcterms.hasVersion'],
    ['dcterms:identifier', 'dcterms.identifier'],
    ['dcterms:instructionalMethod', 'dcterms.instructionalMethod'],
    ['dcterms:isFormatOf', 'dcterms.isFormatOf'],
    ['dcterms:isPartOf', 'dcterms.isPartOf'],
    ['dcterms:isReferencedBy', 'dcterms.isReferencedBy'],
    ['dcterms:isReplacedBy', 'dcterms.isReplacedBy'],
    ['dcterms:isRequiredBy', 'dcterms.isRequiredBy'],
    ['dcterms:issued', 'dcterms.issued'],
    ['dcterms:isVersionOf', 'dcterms.isVersionOf'],
    ['dcterms:language', 'dcterms.language'],
    ['dcterms:license', 'dcterms.license'],
    ['dcterms:mediator', 'dcterms.mediator'],
    ['dcterms:medium', 'dcterms.medium'],
    ['dcterms:modified', 'dcterms.modified'],
    ['dcterms:provenance', 'dcterms.provenance'],
    ['dcterms:publisher', 'dcterms.publisher'],
    ['dcterms:references', 'dcterms.references'],
    ['dcterms:relation', 'dcterms.relation'],
    ['dcterms:replaces', 'dcterms.replaces'],
    ['dcterms:requires', 'dcterms.requires'],
    ['dcterms:rights', 'dcterms.rights'],
    ['dcterms:rightsHolder', 'dcterms.rightsHolder'],
    ['dcterms:source', 'dcterms.source'],
    ['dcterms:spatial', 'dcterms.spatial'],
    ['dcterms:subject', 'dcterms.subject'],
    ['dcterms:tableOfContents', 'dcterms.tableOfContents'],
    ['dcterms:temporal', 'dcterms.temporal'],
    ['dcterms:title', 'dcterms.title'],
    ['dcterms:type', 'dcterms.type'],
    ['dcterms:valid', 'dcterms.valid'],
]);

// Handle clear button.
$('#clear-button').on('click', function(e) {
    $('.meta-name-select').val('').trigger("chosen:updated");
});

// Handle reset button.
$('#reset-button').on('click', function(e) {
    $('.meta-name-select').val('').trigger("chosen:updated");
    $('.meta-name-select').each(function() {
        const thisSelect = $(this);
        thisSelect.val(thisSelect.data('meta-names'));
    });
    $('.meta-name-select').trigger("chosen:updated");
});

// Handle map Dublin Core button.
$('#map-dcterms-button').on('click', function(e) {
    $('.meta-name-select').val('').trigger("chosen:updated");
    for (const [term, metaName] of dctermsMap) {
        $(`.meta-name-select[data-term="${term}"]`).val([metaName]);
    }
    $('.meta-name-select').trigger("chosen:updated");
});

});
