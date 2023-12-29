<?php

namespace JambageCom\MbiProductsCategories\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use JambageCom\MbiProductsCategories\Utility\Tree;
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2021 Franz Holzinger <franz@ttproducts.de>
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
 * This class contains some hooks for processing formdata.
 * Hook for changing the products category field from the number
 * of categories to the first category
 *
 * @package TYPO3
 * @subpackage mbi_products_categories
 *
 * @author 	Franz Holzinger <franz@ttproducts.de>
 * @maintainer	Franz Holzinger <franz@ttproducts.de>
 *
 */
class DmHooks
{
    public function processDatamap_postProcessFieldArray(
        $status,
        $table,
        $id,
        &$fieldArray,
        $pObj
    ) {
        if (strpos($table, 'tt_products') === 0) {
            $row = $pObj->datamap[$table][$id];
            switch ($table) {
                case 'tt_products':
                    $catArray = GeneralUtility::trimExplode(',', $row['category']);
                    reset($catArray);
                    $fieldArray['category'] = intval(current($catArray));
                    break;
                case 'tt_products_cat':
                    $tree = GeneralUtility::makeInstance(Tree::class);

                    $tree->fixRecursion(
                        $table,
                        $id,
                        $row,
                        'parent_category',
                        ['0']
                    );
                    break;
            }
        }
    }
}
