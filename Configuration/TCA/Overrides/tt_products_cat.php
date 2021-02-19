<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function () {
    $pid_list = '';
    $refTable = 'tt_products';
    $mmTable = 'tx_mbiproductscategories_mm';
    $foreigntable = 'tt_products_cat';
    $field = 'category';
    $parentfield = 'parent_category';
    $whereCategory = '';
    $expandAll = 1;

    if (
        isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT]) &&
        is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT])
    ) {
        $pid_list = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT]['pid_list'];
        $refTable = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT]['table'];
        $mmTable = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT]['mmtable'];
        $field = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT]['field'];
        $expandAll = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT]['expandAll'];
    }

    $expandAll = (boolean) $expandAll;

    if (
        isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT]['where.']) &&
        is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT]['where.']) &&
        isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT]['where.']['category'])
    ) {
        $whereCategory = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT]['where.']['category'];
    }

    if (
        $refTable &&
        $mmTable &&
        $field
    ) {
        $where = ($pid_list != '' ? ' AND ' . $foreigntable . '.pid IN (' . $pid_list . ') ' : '') . $whereCategory;
        $where .= \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields($foreigntable);

        $tempColumns = [
            'reference_category' => [
                'exclude' => 1,
                'label' => 'LLL:EXT:mbi_products_categories/locallang_db.xml:tt_products_cat.reference_category',
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
                            'showHeader' => TRUE,
                            'maxLevels' => 99,
                        ]
                    ],
                    'exclude' => 1,
                    'default' => 0
                ],
            ],
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
            $foreigntable,
            $tempColumns,
            1
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            $foreigntable,
            'reference_category;;;;1-1-1',
            '0',
            'after:subtitle'
        );
    }
});

