<?php
defined('TYPO3_MODE') || die('Access denied.');

if (
    version_compare(TYPO3_version, '6.2.0', '>=')
) {
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

        $tempColumns = array (
            'reference_category' => array (
                'exclude' => 1,
                'label' => 'LLL:EXT:mbi_products_categories/locallang_db.xml:tt_products_cat.reference_category',
                'config' => array (
                    'autoSizeMax' => 45,
                    'minitems' => 0,
                    'maxitems' => 1,
                    'type' => 'select',
                    'renderMode' => 'tree',
                    'foreign_table' => $foreigntable,
                    'foreign_table_where' => $where . ' ORDER BY ' . $foreigntable . '.title',
                    'treeConfig' => array(
                        'parentField' => $parentfield,
                        'appearance' => array(
                            'expandAll' => $expandAll,
                            'showHeader' => TRUE,
                            'maxLevels' => 99,
                            'width' => 500,
                        )
                    ),
                    'exclude' => 1,
                    'default' => 0
                ),
            ),
        );

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
}

