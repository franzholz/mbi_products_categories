<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$pid_list = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][MBI_PRODUCTS_CATEGORIES_EXTkey]['pid_list'];

$where = ($pid_list ? ' AND tt_products_cat.pid IN ('.$pid_list.') ' : '');
$tempColumns = Array (
	'parent_category' => Array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:mbi_products_categories/locallang_db.php:tt_products_cat.parent_category',		
		'config' => Array (
			'type' => 'select',
			'form_type' => 'user',
			'userFunc' => 'tx_mbiproductscategories_treeview->displayCategoryTree',
			'treeView' => 1,
			'foreign_table' => 'tt_products_cat',
			'foreign_table_where' => $where.'ORDER BY tt_products_cat.title',	
			'size' => 1,
			'autoSizeMax' => 25,
			'minitems' => 0,
			'maxitems' => 2,

			'exclude' => 1,
		#	'l10n_mode' => 'exclude', // the localizalion mode will be handled by the userfunction
			'label' => 'LLL:EXT:tt_news/locallang_tca.php:tt_news.category',
		),
	),
);


t3lib_div::loadTCA('tt_products_cat');
t3lib_extMgm::addTCAcolumns('tt_products_cat',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tt_products_cat','parent_category;;;;1-1-1');

$TCA['tt_products_cat']['ctrl']['treeParentField'] = 'parent_category';

t3lib_div::loadTCA('tt_products');
$TCA['tt_products']['columns']['category']['config'] = Array(
	'type' => 'select',
	'form_type' => 'user',
	'userFunc' => 'tx_mbiproductscategories_treeview->displayCategoryTree',
	'treeView' => 1,
	'foreign_table' => 'tt_products_cat',
	'foreign_table_where' => $where.'ORDER BY tt_products_cat.title',	
//	'size' => 3,
//	'autoSizeMax' => 25,
//	'minitems' => 0,
//	'maxitems' => 500,
//	'MM' => 'mbi_products_categories_mm',
	'size' => 1,
	'autoSizeMax' => 25,
	'minitems' => 0,
	'maxitems' => 2,
);


if (TYPO3_MODE=='BE')	{
		// class for displaying the category tree in BE forms.
	include_once(PATH_BE_mbiproductscategories.'class.tx_mbiproductscategories_treeview.php');
}

?>
