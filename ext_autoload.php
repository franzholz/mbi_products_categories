<?php
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mbi_products_categories');
return [
	'JambageCom\\MbiProductsCategories\\Hooks\\DmHooks' => $extensionPath . 'Classes/Hooks/DmHooks.php',
	'JambageCom\\MbiProductsCategories\\Utility\\Category' => $extensionPath . 'Classes/Utility/Category.php',
	'JambageCom\\MbiProductsCategories\\Utility\\ItemArray' => $extensionPath . 'Classes/Utility/ItemArray.php',
	'JambageCom\\MbiProductsCategories\\Utility\\Tree' => $extensionPath . 'Classes/Utility/Tree.php',
	'JambageCom\\MbiProductsCategories\\View\\TceFuncTreeView' => $extensionPath . 'Classes/View/TceFuncTreeView.php',
];

