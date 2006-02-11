<?php
if (!defined ('TYPO3_MODE'))	die ('Access denied.');

if (!defined ('MBI_PRODUCTS_CATEGORIES_EXTkey')) {
	define('MBI_PRODUCTS_CATEGORIES_EXTkey',$_EXTKEY);
}

if (!defined ('TT_PRODUCTS_EXTkey')) {
	define('TT_PRODUCTS_EXTkey','tt_products');
}

if (!defined ('PATH_BE_mbiproductscategories')) {
	define('PATH_BE_mbiproductscategories', t3lib_extMgm::extPath(MBI_PRODUCTS_CATEGORIES_EXTkey));
}

if (!defined ('PATH_BE_ttproducts_rel')) {
	define('PATH_BE_ttproducts_rel', t3lib_extMgm::extRelPath(TT_PRODUCTS_EXTkey));
}

if (!defined ('PATH_FE_ttproducts_rel')) {
	define('PATH_FE_ttproducts_rel', t3lib_extMgm::siteRelPath(TT_PRODUCTS_EXTkey));
}

if (!defined ('PATH_ttproducts_icon_table_rel')) {
	define('PATH_ttproducts_icon_table_rel', PATH_BE_ttproducts_rel.'res/icons/table/');
}

if (!defined ('TABLE_EXTkey')) {
	define('TABLE_EXTkey','table');
}

if (!defined ('PATH_BE_table')) {
	define('PATH_BE_table', t3lib_extMgm::extPath(TABLE_EXTkey));
}

if (!defined ('FH_LIBRARY_EXTkey')) {
	define('FH_LIBRARY_EXTkey','fh_library');
}


if (!defined ('MBI_PRODUCTS_CATEGORIES_DIV_DLOG')) {
	define('MBI_PRODUCTS_CATEGORIES_DIV_DLOG', '0');	// for development error logging
}


?>
