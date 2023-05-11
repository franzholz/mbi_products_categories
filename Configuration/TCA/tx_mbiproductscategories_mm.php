<?php
defined('TYPO3') || die('Access denied.');

$extensionKey = 'tx_mbiproducts_categories';

$result = [
    'ctrl' => [
        'title' => 'LLL:EXT:' . $extensionKey . DIV2007_LANGUAGE_SUBPATH . 'locallang_db.xlf:tx_mbiproductscategories_mm',
        'label' => 'uid_local',
        'tstamp' => 'tstamp',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'prependAtCopy' => DIV2007_LANGUAGE_LGL . 'prependAtCopy',
        'crdate' => 'crdate',
        'iconfile' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/' . 'tt_products_cat.gif',
        'hideTable' => true,
    ],
    'columns' => [
        'uid_local' => [
            'label' => 'LLL:EXT:' . $extensionKey . DIV2007_LANGUAGE_SUBPATH . 'locallang_db.xlf:tx_mbiproductscategories_mm.uid_local',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tt_products',
                'maxitems' => 1,
                'default' => 0
            ]
        ],
        'uid_foreign' => [
            'label' => 'LLL:EXT:' . $extensionKey . DIV2007_LANGUAGE_SUBPATH . 'locallang_db.xlf:tx_mbiproductscategories_mm.uid_foreign',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tt_products_cat',
                'maxitems' => 1,
                'default' => 0
            ]
        ],
        'sorting' => [
            'config' => [
                'type' => 'passthrough',
                'default' => 0
            ]
        ],
        'sorting_foreign' => [
            'config' => [
                'type' => 'passthrough',
                'default' => 0
            ]
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => ''
        ]
    ]
];

if (
    defined('TYPO3_version') &&
    version_compare(TYPO3_version, '10.0.0', '<')
) {
    $result['interface'] = [];
    $result['interface']['showRecordFieldList'] = 'uid_local,uid_foreign';
}

return $result;
