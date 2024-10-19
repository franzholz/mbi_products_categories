<?php

defined('TYPO3') || die('Access denied.');

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(function ($extensionKey, $table): void {
    $languageSubpath = '/Resources/Private/Language/';

    if (!ExtensionManagementUtility::isLoaded('tt_products')) {
        return;
    }

    $pid_list = '';
    $refTable = 'tt_products';
    $mmTable = 'tx_mbiproductscategories_mm';
    $foreigntable = $table;
    $field = 'category';
    $parentfield = 'parent_category';
    $whereCategory = '';
    $expandAll = 1;

    if (
        isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]) &&
        is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey])
    ) {
        $pid_list = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['pid_list'];
        $refTable = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['table'];
        $mmTable = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['mmtable'];
        $field = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['field'];
        $expandAll = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['expandAll'];
    }

    $expandAll = (bool) $expandAll;

    if (
        isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['where.']) &&
        is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['where.']) &&
        isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['where.']['category'])
    ) {
        $whereCategory = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['where.']['category'];
    }

    if (
        $refTable &&
        $mmTable &&
        $field
    ) {
        $where = ($pid_list != '' ? ' AND ' . $foreigntable . '.pid IN (' . $pid_list . ') ' : '') . $whereCategory;
        $tempColumns = [
            'reference_category' => [
                'exclude' => 1,
                'label' => 'LLL:EXT:mbi_products_categories' . $languageSubpath . 'locallang_db.xlf:tt_products_cat.reference_category',
                'config' => [
                    'size' => 15,
                    'minitems' => 0,
                    'maxitems' => 1,
                    'type' => 'select',
                    'renderType' => 'selectTree',
                    'foreign_table' => $foreigntable,
                    'foreign_table_where' => $where . ' ORDER BY ' . $foreigntable . '.title',
                    'treeConfig' => [
                        'parentField' => $parentfield,
                        'appearance' => [
                            'expandAll' => $expandAll,
                            'showHeader' => true,
                            'maxLevels' => 99,
                        ]
                    ],
                    'exclude' => 1,
                    'default' => 0
                ],
            ],
        ];

        ExtensionManagementUtility::addTCAcolumns(
            $foreigntable,
            $tempColumns
        );
        ExtensionManagementUtility::addToAllTCAtypes(
            $foreigntable,
            'reference_category',
            '0',
            'after:subtitle'
        );
    }
}, 'mbi_products_categories', basename(__FILE__, '.php'));
