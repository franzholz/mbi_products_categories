<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$tempColumns = Array (
	"parent_category" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:mbi_products_categories/locallang_db.php:tt_products_cat.parent_category",		
		'config' => Array (
			'type' => 'select',
			'form_type' => 'user',
			'userFunc' => 'tx_mbiproductscategories_treeview->displayCategoryTree',
			'treeView' => 1,
			'foreign_table' => 'tt_products_cat',
			"foreign_table_where" => "ORDER BY tt_products_cat.uid",	
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


t3lib_div::loadTCA("tt_products_cat");
t3lib_extMgm::addTCAcolumns("tt_products_cat",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("tt_products_cat","parent_category;;;;1-1-1");


t3lib_div::loadTCA("tt_products");
$TCA["tt_products"]["columns"]["category"]["config"] = Array(
			'type' => 'select',
			'form_type' => 'user',
			'userFunc' => 'tx_mbiproductscategories_treeview->displayCategoryTree',
			'treeView' => 1,
			'foreign_table' => 'tt_products_cat',
			"foreign_table_where" => "ORDER BY tt_products_cat.uid",	
/* 			'size' => 3, */
/* 			'autoSizeMax' => 25, */
/* 			'minitems' => 0, */
/* 			'maxitems' => 500, */
/* 			'MM' => 'tt_products_cat_mm', */
			'size' => 1,
			'autoSizeMax' => 25,
			'minitems' => 0,
			'maxitems' => 2,
			);


if (TYPO3_MODE=='BE')	{
		// class for displaying the category tree in BE forms.
	include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_mbiproductscategories_treeview.php');
}

?>
