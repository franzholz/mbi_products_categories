<?php
defined('TYPO3') || die('Access denied.');

call_user_func(function () {
    if (!defined ('MBI_PRODUCTS_CATEGORIES_EXT')) {
        define('MBI_PRODUCTS_CATEGORIES_EXT', 'mbi_products_categories');
    }

    $extensionConfiguration = [];
    $originalConfiguration = [];

    $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get(MBI_PRODUCTS_CATEGORIES_EXT);

    if (!defined ('PATH_MBIPRODUCTSCATEGORIES_ICON_TABLE_REL')) {
        define('PATH_MBIPRODUCTSCATEGORIES_ICON_TABLE_REL', 'EXT:' . MBI_PRODUCTS_CATEGORIES_EXT . '/Resources/Public/Icons/');
    }

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT] = $extensionConfiguration;

    // Hook for extending the products list
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_products']['prodCategory'][] = \JambageCom\MbiProductsCategories\Utility\Category::class;

    // Hooks for datamap procesing
    // for changing the category field from the number of catogories to the first category
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \JambageCom\MbiProductsCategories\Hooks\DmHooks::class;
});
