<?php

namespace JambageCom\MbiProductsCategories\Utility;


/***************************************************************
*  Copyright notice
*
*  (c) 2007-2016 Franz Holzinger <franz@ttproducts.de>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Part of the mbi_products_categories (Hierarchical Categories) extension.
 *
 * functions for the TYPO3 menues
 *
 * @author  Franz Holzinger <franz@ttproducts.de>
 * @package TYPO3
 * @subpackage mbi_products_categories
 *
 *
 */


class ItemArray {
    public function procFunc ($menuArr, $conf)
    {
		$where_clause = '';
		$rowArray = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tt_products_cat',
			$where_clause
		);
		return $menuArr;
	}
}

