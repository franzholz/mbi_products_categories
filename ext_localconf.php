<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function () {
    if (!defined ('MBI_PRODUCTS_CATEGORIES_EXT')) {
        define('MBI_PRODUCTS_CATEGORIES_EXT', 'mbi_products_categories');
    }

    $extensionConfiguration = [];
    $originalConfiguration = [];

    if (
        defined('TYPO3_version') &&
        version_compare(TYPO3_version, '9.0.0', '>=')
    ) {
        $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
        )->get(MBI_PRODUCTS_CATEGORIES_EXT);
    } else { // before TYPO3 9
        $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][MBI_PRODUCTS_CATEGORIES_EXT]);
    }

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT] = $extensionConfiguration;

    // Hook for extending the products list
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_products']['prodCategory'][] = \JambageCom\MbiProductsCategories\Utility\Category::class;

    // Hooks for datamap procesing
    // for changing the category field from the number of catogories to the first category
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \JambageCom\MbiProductsCategories\Hooks\DmHooks::class;
});
