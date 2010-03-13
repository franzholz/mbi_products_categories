<?php

########################################################################
# Extension Manager/Repository config file for ext: "mbi_products_categories"
#
# Auto generated 05-08-2009 10:44
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Shop Categories',
	'description' => 'Enables hierarchical categories for products. Works with every version of tt_products.',
	'category' => 'misc',
	'shy' => '',
	'version' => '0.1.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'alpha',
	'uploadfolder' => '',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => '',
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
	'_md5_values_when_last_written' => 'a:11:{s:9:"ChangeLog";s:4:"5220";s:43:"class.tx_mbiproductscategories_treeview.php";s:4:"d77b";s:21:"ext_conf_template.txt";s:4:"6690";s:12:"ext_icon.gif";s:4:"f852";s:17:"ext_localconf.php";s:4:"1383";s:14:"ext_tables.php";s:4:"3981";s:14:"ext_tables.sql";s:4:"da89";s:16:"locallang_db.php";s:4:"9eb9";s:19:"doc/wizard_form.dat";s:4:"aca8";s:20:"doc/wizard_form.html";s:4:"fc5a";s:61:"lib/class.tx_mbiproductscategories_tcefunc_selecttreeview.php";s:4:"3cd3";}',
);

?>