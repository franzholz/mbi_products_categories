<?php

########################################################################
# Extension Manager/Repository config file for ext "mbi_products_categories".
#
# Auto generated 27-09-2010 11:42
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Shop Categories',
	'description' => 'Enables hierarchical categories for products. Works with every version of tt_products.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '0.1.3',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Christian Lang',
	'author_email' => 'christian.lang@mbi.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.5.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:9:"ChangeLog";s:4:"17e9";s:43:"class.tx_mbiproductscategories_treeview.php";s:4:"4d6f";s:21:"ext_conf_template.txt";s:4:"6690";s:12:"ext_icon.gif";s:4:"b6f5";s:17:"ext_localconf.php";s:4:"4607";s:14:"ext_tables.php";s:4:"eff9";s:14:"ext_tables.sql";s:4:"da89";s:16:"locallang_db.php";s:4:"9eb9";s:14:"doc/manual.sxw";s:4:"955d";s:61:"lib/class.tx_mbiproductscategories_tcefunc_selecttreeview.php";s:4:"e318";}',
);

?>