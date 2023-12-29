<?php

namespace JambageCom\MbiProductsCategories\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
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
 * Part of the mbi_products_categories (Hierarchical Categories) extension.
 *
 * functions for the category
 *
 * @author  Franz Holzinger <franz@ttproducts.de>
 * @package TYPO3
 * @subpackage mbi_products_categories
 *
 *
 */
class Tree implements SingletonInterface
{
    public function fixRecursion($table, $uid, array $newRow, $parentField, array $endArray)
    {
        $uidArray = [];
        $cat = $uid;
        $count = 0;

        $where .= BackendUtility::BEenableFields($table);
        $where_clause = 'uid = ' . intval($cat) . ' ' . $where;
        // Fetching the categories
        $rowArray =
            $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                'uid cat, ' . $parentField . ' uid_parent',
                $table,
                $where_clause
            );

        if (
            isset($rowArray) &&
            is_array($rowArray) &&
            isset($rowArray['0']) &&
            isset($rowArray['0']['uid_parent']) &&
            $rowArray['0']['uid_parent'] != $newRow[$parentField]
        ) {
            $formerParent = intval($rowArray['0']['uid_parent']);
            $endArray[] = $cat;
            $endArray[] = $formerParent;
            $parent = $newRow[$parentField];
            $uidArray = [];
            $count = 0;

            do {
                $where_clause = 'uid = ' . intval($parent) . ' ' . $where;
                // Fetching the categories
                $rowArray =
                    $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                        'uid cat, ' . $parentField . ' uid_parent',
                        $table,
                        $where_clause
                    );
                $row = $rowArray[0];
                $parent = $row['uid_parent'];
                $count++;
            } while ($parent && !in_array($parent, $endArray) && $count <= 500);

            if ($parent == $uid) { // loop detected
                $uidArray[] = $parent;
                $fields_values = [];
                $fields_values[$parentField] = $formerParent; // Make the former parent of the current record the parent of the found record. This will remove the recursive loop.
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
                    $table,
                    $where_clause,
                    $fields_values
                );
            }
        }

        return $uidArray;
    }
}
