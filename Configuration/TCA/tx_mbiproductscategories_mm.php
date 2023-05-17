<?php
defined('TYPO3') || die('Access denied.');

$extensionKey = 'mbi_products_categories';
$languageSubpath = '/Resources/Private/Language/';
$localTable = 'tt_products';
$foreignTable = 'tt_products_cat';

$result = [
    'ctrl' => [
        'title' => 'LLL:EXT:' . $extensionKey . $languageSubpath . 'locallang_db.xlf:tx_mbiproductscategories_mm',
        'label' => 'uid_local',
        'tstamp' => 'tstamp',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'prependAtCopy' => 'LLL:EXT:core' . $languageSubpath . 'locallang_general.xlf:LGL.prependAtCopy',
        'crdate' => 'crdate',
        'iconfile' => 'EXT:' . $extensionKey . '/Resources/Public/Icons/' . 'tt_products_cat.gif',
        'hideTable' => true,
    ],
    'columns' => [
        'uid_local' => [
            'label' => 'LLL:EXT:' . $extensionKey . $languageSubpath . 'locallang_db.xlf:tx_mbiproductscategories_mm.uid_local',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => $localTable,
                'maxitems' => 1,
                'default' => 0
            ]
        ],
        'uid_foreign' => [
            'label' => 'LLL:EXT:' . $extensionKey . $languageSubpath . 'locallang_db.xlf:tx_mbiproductscategories_mm.uid_foreign',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => $foreignTable,
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

return $result;
