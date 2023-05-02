<?php
namespace ResourceMeta;

use Laminas\Router\Http;

return [
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => sprintf('%s/../language', __DIR__),
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            sprintf('%s/../view', __DIR__),
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            sprintf('%s/../src/Entity', __DIR__),
        ],
        'proxy_paths' => [
            sprintf('%s/../data/doctrine-proxies', __DIR__),
        ],
    ],
    'service_manager' => [
        'factories' => [
            'ResourceMeta\ResourceMeta' => Service\Stdlib\ResourceMetaFactory::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            'ResourceMeta\Controller\Admin\Index' => Controller\Admin\IndexController::class,
        ],
        'factories' => [
            'ResourceMeta\Controller\Admin\ResourceTemplate' => Service\Controller\Admin\ResourceTemplateControllerFactory::class,
        ],
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Resource Meta', // @translate
                'route' => 'admin/resource-meta',
                'controller' => 'index',
                'action' => 'index',
                'resource' => 'ResourceMeta\Controller\Admin\Index',
                'useRouteMatch' => true,
                'pages' => [
                    [
                        'route' => 'admin/resource-meta-resource-template',
                        'visible' => false,
                    ],
                    [
                        'route' => 'admin/resource-meta-resource-template/id',
                        'visible' => false,
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'resource-meta' => [
                        'type' => Http\Literal::class,
                        'options' => [
                            'route' => '/resource-meta',
                            'defaults' => [
                                '__NAMESPACE__' => 'ResourceMeta\Controller\Admin',
                                'controller' => 'index',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'resource-meta-resource-template' => [
                        'type' => Http\Segment::class,
                        'options' => [
                            'route' => '/resource-meta/resource-template/:action',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'ResourceMeta\Controller\Admin',
                                'controller' => 'resource-template',
                                'action' => 'browse',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'id' => [
                                'type' => Http\Segment::class,
                                'options' => [
                                    'route' => '/:id',
                                    'constraints' => [
                                        'id' => '\d+',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    // @see http://div.div1.com.au/div-thoughts/div-commentaries/66-div-commentary-metadata
    'resource_meta_meta_names' => [
        'be_press' => [
            'label' => 'BE Press', // @translate
            'meta_names' => [
                'bepress_citation_abstract_html_url',
                'bepress_citation_author',
                'bepress_citation_author_institution',
                'bepress_citation_date',
                'bepress_citation_doi',
                'bepress_citation_firstpage',
                'bepress_citation_issn',
                'bepress_citation_issue',
                'bepress_citation_journal_title',
                'bepress_citation_lastpage',
                'bepress_citation_online_date',
                'bepress_citation_pdf_url',
                'bepress_citation_publisher',
                'bepress_citation_series_title',
                'bepress_citation_title',
                'bepress_citation_volume',
                'bepress_is_article_cover_page',
            ],
        ],
        'dc_elements' => [
            'label' => 'Dublin Core Elements', // @translate
            'meta_names' => [
                'dc.contributor',
                'dc.coverage',
                'dc.creator',
                'dc.date',
                'dc.description',
                'dc.format',
                'dc.identifier',
                'dc.language',
                'dc.publisher',
                'dc.relation',
                'dc.rights',
                'dc.source',
                'dc.subject',
                'dc.title',
                'dc.type',
            ],
        ],
        'dc_terms' => [
            'label' => 'Dublin Core Terms', // @translate
            'meta_names' => [
                'dcterms.abstract',
                'dcterms.accessRights',
                'dcterms.accrualMethod',
                'dcterms.accrualPeriodicity',
                'dcterms.accrualPolicy',
                'dcterms.alternative',
                'dcterms.audience',
                'dcterms.available',
                'dcterms.conformsTo',
                'dcterms.contributor',
                'dcterms.coverage',
                'dcterms.created',
                'dcterms.creator',
                'dcterms.date',
                'dcterms.dateAccepted',
                'dcterms.dateCopyrighted',
                'dcterms.dateSubmitted',
                'dcterms.description',
                'dcterms.educationLevel',
                'dcterms.extent',
                'dcterms.format',
                'dcterms.hasFormat',
                'dcterms.hasPart',
                'dcterms.hasVersion',
                'dcterms.identifier',
                'dcterms.instructionalMethod',
                'dcterms.isFormatOf',
                'dcterms.isPartOf',
                'dcterms.isReferencedBy',
                'dcterms.isReplacedBy',
                'dcterms.isRequiredBy',
                'dcterms.issued',
                'dcterms.isVersionOf',
                'dcterms.language',
                'dcterms.license',
                'dcterms.mediator',
                'dcterms.medium',
                'dcterms.modified',
                'dcterms.provenance',
                'dcterms.publisher',
                'dcterms.references',
                'dcterms.relation',
                'dcterms.replaces',
                'dcterms.requires',
                'dcterms.rights',
                'dcterms.rightsHolder',
                'dcterms.source',
                'dcterms.spatial',
                'dcterms.subject',
                'dcterms.temporal',
                'dcterms.title',
                'dcterms.type',
                'dcterms.valid',
            ],
        ],
        'eprints' => [
            'label' => 'Eprints', // @translate
            'meta_names' => [
                'eprints.abstract',
                'eprints.citation',
                'eprints.creators_name',
                'eprints.datestamp',
                'eprints.date',
                'eprints.date_type',
                'eprints.id_number',
                'eprints.ispublished',
                'eprints.issn',
                'eprints.number',
                'eprints.official_url',
                'eprints.pagerange',
                'eprints.publication',
                'eprints.publisher',
                'eprints.title',
                'eprints.type',
                'eprints.volume',
            ],
        ],
        'highwire_press' => [
            'label' => 'Highwire Press', // @translate
            'meta_names' => [
                'citation_abstract_html_url',
                'citation_abstract_pdf_url',
                'citation_author',
                'citation_author_email',
                'citation_author_institution',
                'citation_author_orcid',
                'citation_authors',
                'citation_collection_id',
                'citation_conference_title',
                'citation_date',
                'citation_dissertation_institution',
                'citation_dissertation_name',
                'citation_doi',
                'citation_firstpage',
                'citation_fulltext_html_url',
                'citation_fulltext_world_readable',
                'citation_id_from_sass_path',
                'citation_inbook_title',
                'citation_isbn',
                'citation_issn',
                'citation_issue',
                'citation_journal_title',
                'citation_keywords',
                'citation_language',
                'citation_lastpage',
                'citation_online_date',
                'citation_patent_country',
                'citation_patent_number',
                'citation_pdf_url',
                'citation_pmid',
                'citation_price',
                'citation_publication_date',
                'citation_publisher',
                'citation_public_url',
                'citation_reference',
                'citation_section',
                'citation_technical_report_institution',
                'citation_technical_report_number',
                'citation_title',
                'citation_volume',
                'citation_year',
            ],
        ],
        'prism' => [
            'label' => 'PRISM', // @translate
            'meta_names' => [
                'prism.academicField',
                'prism.adultContentWarning',
                'prism.agreement',
                'prism.aggregateIssueNumber',
                'prism.aggregationType',
                'prism.alternateTitle',
                'prism.blogTitle',
                'prism.blogURL',
                'prism.bookEdition',
                'prism.byteCount',
                'prism.channel',
                'prism.complianceProfile',
                'prism.contactInfo',
                'prism.contentType',
                'prism.copyright',
                'prism.corporateEntity',
                'prism.copyrightYear',
                'prism.coverDate',
                'prism.coverDisplayDate',
                'prism.creationDate',
                'prism.creditLine',
                'prism.dateReceived',
                'prism.device',
                'prism.distributor',
                'prism.doi',
                'prism.edition',
                'prism.eIssn',
                'prism.embargoDate',
                'prism.endingPage',
                'prism.event',
                'prism.exclusivityEndDate',
                'prism.expirationDate',
                'prism.genre',
                'prism.hasAlternative',
                'prism.hasCorrection',
                'prism.hasTranslation',
                'prism.imageSizeRestriction',
                'prism.industry',
                'prism.isAlternativeOf',
                'prism.isbn',
                'prism.isCorrectionOf',
                'prism.issn',
                'prism.issueIdentifier',
                'prism.issueName',
                'prism.issueTeaser',
                'prism.issueType',
                'prism.isTranslationOf',
                'prism.keyword',
                'prism.killDate',
                'prism.link',
                'prism.location',
                'prism.modificationDate',
                'prism.nationalCatalogNumber',
                'prism.number',
                'prism.object',
                'prism.offSaleDate',
                'prism.onSaleDate',
                'prism.onSaleDay',
                'prism.optionEndDate',
                'prism.organization',
                'prism.originPlatform',
                'prism.pageCount',
                'prism.pageProgressionDirection',
                'prism.pageRange',
                'prism.permissions',
                'prism.person',
                'prism.place',
                'prism.platform',
                'prism.place',
                'prism.productCode',
                'prism.profession',
                'prism.publicationDate',
                'prism.publicationDisplayDate',
                'prism.publicationName',
                'prism.publishingFrequency',
                'prism.rating',
                'prism.restrictions',
                'prism.reuseProhibited',
                'prism.rightsAgent',
                'prism.rightsOwner',
                'prism.role',
                'prism.role',
                'prism.samplePageRange',
                'prism.section',
                'prism.sellingAgency',
                'prism.seriesNumber',
                'prism.seriesTitle',
                'prism.sport',
                'prism.startingPage',
                'prism.subchannel1',
                'prism.subchannel2',
                'prism.subchannel3',
                'prism.subchannel4',
                'prism.subsection1',
                'prism.subsection2',
                'prism.subsection3',
                'prism.subsection4',
                'prism.subtitle',
                'prism.supplementDisplayID',
                'prism.supplementStartingPage',
                'prism.supplementTitle',
                'prism.teaser',
                'prism.ticker',
                'prism.timePeriod',
                'prism.uspsNumber',
                'prism.url',
                'prism.versionIdentifier',
                'prism.volume',
                'prism.wordCount',
            ],
        ],
        'seo' => [
            'label' => 'Search Engine Optimizations', // @translate
            'meta_names' => [
                'description',
                'author',
            ],
        ],
    ],
];
