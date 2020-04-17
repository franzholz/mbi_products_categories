<?php
defined('TYPO3_MODE') || die('Access denied.');

if (!defined ('MBI_PRODUCTS_CATEGORIES_EXT')) {
    define('MBI_PRODUCTS_CATEGORIES_EXT', 'mbi_products_categories');
}

if (!defined ('PATH_BE_mbiproductscategories')) {
    define('PATH_BE_mbiproductscategories', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(MBI_PRODUCTS_CATEGORIES_EXT));
}

$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXT] = $_EXTCONF;
$version = 0;

if (
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('div2007') &&
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_products')
) {
    $eInfo = \JambageCom\Div2007\Utility\ExtensionUtility::getExtensionInfo('tt_products');
    $version = $eInfo['version'];
}

if (version_compare($version, '2.7.3', '>=')) {
    // Hook for extending the products list
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_products']['prodCategory'][] = 'JambageCom\\MbiProductsCategories\\Utility\\Category';

    // Hooks for datamap procesing
    // for changing the category field from the number of catogories to the first category
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'JambageCom\\MbiProductsCategories\\Hooks\\DmHooks';
}

