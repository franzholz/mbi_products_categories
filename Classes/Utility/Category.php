<?php

namespace JambageCom\MbiProductsCategories\Utility;

/***************************************************************
*  Copyright notice
*
*  (c) 2007-2019 Franz Holzinger <franz@ttproducts.de>
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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Category implements SingletonInterface
{
    public function addWhereCat(
        $prodObject,
        $catObject,
        $cat,
        $where,
        &$operator,
        $pid_list,
        $depth = 1,
        $andCat = ''
    ) {
        $where = '';
        if($cat != '' || $andCat != '') {
            $uidArray = [];
            $tableObj = $prodObject->getTableObj();
            $this->getUidsCat(
                $prodObject,
                $catObject,
                $uidArray,
                $cat,
                $pid_list,
                $depth,
                $andCat
            );

            if (count($uidArray)) {
                $where = $tableObj->aliasArray[$tableObj->getName()] . '.uid IN (' . implode(',', $uidArray) . ')';
            }
            $operator = 'AND';
        }
        return $where;
    }

    // returns the child category array
    public function addselectConfCat(
        $prodObject,
        $catObject,
        $cat,
        &$selectConf,
        $depth
    ) {
        $startCat = $cat;
        $childCatArray = [];
        $catArray = GeneralUtility::trimExplode(',', $cat);
        foreach ($catArray as $loopCat) {
            $childCatArray[$loopCat] = [];
        }

        $tableObj = $prodObject->getTableObj();
        $prodAlias = $tableObj->getAlias();

        $selectConf['leftjoin'] = $catObject->getMMTablename() . ' ON ' . $prodAlias . '.uid = ' . $catObject->getMMTablename() . '.uid_local';

        if ($cat != '') {
            if ($depth > 1) {
                $tablename = $tableObj->getName();
                $depthArray = [];
                $depthArray[] = 1;
                $currentPointer = 0;
                $currentDepth = 0;
                $counter = 0;
                while ($currentPointer < count($catArray) && $counter < 500) {
                    $counter++;
                    $currentCat = $catArray[$currentPointer];
                    $currentDepth = $depthArray[$currentPointer];
                    $currentPointer++;
                    if ($currentDepth == $depth) {
                        continue; // maximum depth has been reached.
                    }
                    $childArray = $this->getChildCategories($catObject, $currentCat);
                    $nextDepth = $currentDepth + 1;
                    foreach ($childArray as $childRow) {
                        $catArray[] = $childRow['cat'];
                        $depthArray[] = $nextDepth;
                        $childCatArray[$currentCat][] = $childRow['cat'];
                    }
                }
                $cat = implode(',', $catArray);
            }
            $selectConf['where'] = (!empty($selectConf['where']) ? $selectConf['where'] . ' AND ' : '') . $catObject->getMMTablename() . '.uid_foreign IN (' . $cat . ')';
            $additionalTable = $tableObj->getAdditionalTables();
            $selectConf['from'] = (!empty($selectConf['from']) ? $selectConf['from'] . ', ' : '') . $additionalTable;
        }

        if ($depth == 0) {
            $selectConf['where'] = '1=0'; // nothing shall be shown with depth = 0
        }

        return $childCatArray;
    }


    public function addConfCatProduct($prodObject, $catObject, &$selectConf, $aliasArray)
    {
        $catTableObj = $catObject->getTableObj();
        $prodAlias = $prodObject->getAlias();
        $catAlias = $catObject->getAlias();
        $tableDesc = $prodObject->getTableDesc();

        // FROM tt_products product LEFT OUTER JOIN tx_mbiproductscategories_mm mm_cat1 ON product.uid = mm_cat1.uid_local LEFT OUTER JOIN tt_products_cat tt_products_cat ON mm_cat1.uid_foreign = tt_products_cat.uid

        $selectConf['leftjoin'] = $catObject->getMMTablename() . ' ' . $aliasArray['mm1'] . ' ON ' . $prodAlias . '.uid = ' . $aliasArray['mm1'] . '.uid_local LEFT JOIN ' . $catTableObj->getName() . ' ' . $catAlias . ' ON ' . $aliasArray['mm1'] . '.uid_foreign = ' . $catAlias . '.uid';

        $this->addConfAdditionalTables($prodObject, $catObject, $selectConf);
    }

    public function addConfAdditionalTables($prodObject, $catObject, &$selectConf)
    {
        $prodTableObj = $prodObject->getTableObj();
        $catTableObj = $catObject->getTableObj();

        $additionalTable = $prodTableObj->getAdditionalTables();

        if ($additionalTable != '') {

            $additionalTableArray = GeneralUtility::trimExplode(',', $additionalTable);
            $additionalTableArray = array_diff($additionalTableArray, [$catTableObj->getName()]);
            $additionalTable = implode(',', $additionalTableArray);
            $selectConf['from'] = ($selectConf['from'] ? $selectConf['from'] . ', ' : '') . $additionalTable;
            $tableArray = GeneralUtility::trimExplode(',', $selectConf['from']);
            $tableArray = array_unique($tableArray);
            $selectConf['from'] = implode(',', $tableArray);
        }
    }

    protected function getUidsCatSingle(
        $prodObject,
        $catObject,
        &$uidArray,
        &$childCatArray,
        $cat,
        $pid_list,
        $depth,
        $where
    ) {
        $tableObj = $prodObject->getTableObj();
        $selectConf = [];
        $local_cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        if ($where != '') {
            $selectConf['where'] = $where;
        }

        $childCatArray = $this->addselectConfCat(
            $prodObject,
            $catObject,
            $cat,
            $selectConf,
            $depth
        );
        $selectConf['where'] .= ' ' . $tableObj->enableFields();
        $selectConf['pidInList'] = $pid_list;
        $selectConf['selectFields'] = 'uid';
        $queryParts =
            $tableObj->getQueryConf(
                $local_cObj,
                $tableObj->getName(),
                $selectConf,
                true
            );
        $res = $tableObj->exec_SELECT_queryArray($queryParts);

        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $uidArray[] = $row['uid'];
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);
        if (!count($uidArray)) {
            $uidArray[] = '0'; // nothing shall be shown
        }
    }


    public function getUidsCat(
        $prodObject,
        $catObject,
        &$uidArray,
        $cat,
        $pid_list,
        $depth = 1,
        $andCat = ''
    ) {
        $uids = '';
        $oldUidArray = $uidArray;
        $newUidArray =
            [
                'cat' => [],
                'andCat' => []
            ];

        $childCatArray = [];
        if ($cat != '') {
            $this->getUidsCatSingle(
                $prodObject,
                $catObject,
                $newUidArray['cat'],
                $childCatArray,
                $cat,
                $pid_list,
                $depth,
                ''
            );
            $uids = implode(',', $newUidArray['cat']);
        }

        if ($andCat != '' && $uids != '0') {
            $where = '';

            if ($uids != '') {
                $where =
                    $catObject->getMMTablename() . '.uid_local IN (' . $uids . ')';
            }
            $childCatArray = [];

            $this->getUidsCatSingle(
                $prodObject,
                $catObject,
                $newUidArray['andCat'],
                $childCatArray,
                $andCat,
                $pid_list,
                $depth,
                $where
            );

            $andCatArray = GeneralUtility::trimExplode(',', $andCat);
            $andCatArray = array_unique($andCatArray);
            $uids = implode(',', $newUidArray['andCat']);

            if ($uids != '') {

                $catArray = [];

                foreach ($childCatArray as $loopCat => $childArray) {
                    $catArray[] = $loopCat;
                    $catArray = array_merge($catArray, $childArray);
                }
                $catlist = implode(',', $catArray);
                $whereClause = 'uid_local IN (' . $uids . ') AND uid_foreign IN (' . $catlist . ')';
                $selectFields = 'uid_local prodid,uid_foreign catid';

                $res =
                    $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                        $selectFields,
                        $catObject->getMMTablename(),
                        $whereClause
                    );

                $prodCatArray = [];

                while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                    $prodid = $row['prodid'];
                    $catid = $row['catid'];
                    if (!isset($prodCatArray[$prodid])) {
                        $prodCatArray[$prodid] = [];
                    }
                    $prodCatArray[$prodid][] = $catid;
                }
                $GLOBALS['TYPO3_DB']->sql_free_result($res);
                $uidArray = [];

                if (count($prodCatArray)) {
                    foreach ($prodCatArray as $prodid => $loopCatArray) {
                        $bMatchCategoryArray = [];
                        foreach ($andCatArray as $loopAndCat) {
                            foreach ($loopCatArray as $innerLoopCat) {
                                if (!isset($bMatchCategoryArray[$loopAndCat])) {
                                    if ($loopAndCat == $innerLoopCat) {
                                        $bMatchCategoryArray[$loopAndCat] = true;
                                        break;
                                    } elseif (
                                        isset($childCatArray[$loopAndCat]) &&
                                        is_array($childCatArray[$loopAndCat])
                                    ) {
                                        foreach ($childCatArray[$loopAndCat] as $loopChildCat) {
                                            if ($loopChildCat == $innerLoopCat) {
                                                $bMatchCategoryArray[$loopAndCat] = true;
                                                break;
                                            } elseif (
                                                isset($childCatArray[$loopChildCat]) &&
                                                is_array($childCatArray[$loopChildCat])
                                            ) {
                                                foreach ($childCatArray[$loopChildCat] as $innerLoopChildCat) {
                                                    if ($innerLoopChildCat == $innerLoopCat) {
                                                        $bMatchCategoryArray[$loopAndCat] = true;
                                                        break;
                                                    } else {
                                                        // TODO
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (count($andCatArray) == count($bMatchCategoryArray)) {
                            $uidArray[] = $prodid;
                        }
                    }
                }

                if (!count($uidArray)) {
                    $uidArray = ['0'];
                }
            } else {
                $uidArray = ['0'];
            }
        } elseif ($cat != '') {
            $uidArray = $newUidArray['cat'];
        }
    }

    public function getCategories(
        $object,
        $uid,
        $mm_table = 'tx_mbiproductscategories_mm',
        $orderBy = ''
    ) {
        $where_clause = $mm_table . ' . uid_local=' . intval($uid);
        // Fetching the categories
        if ($orderBy) {
            $tableObj = $object->getTableObj();
            $tablename = $tableObj->getName();
            $aliasname = $tableObj->getAlias();
            $table = $mm_table . ' LEFT JOIN ' . $tablename . ' ' . $aliasname . ' ON ' . $mm_table . '.uid_foreign=' . $aliasname . '.uid';
            $orderBy = $tableObj->transformOrderby($orderBy);
            $select = 'DISTINCT ' . $mm_table . '.uid_foreign cat';
            $tableObj->transformLanguage($table, $where_clause);
            $rowArray =
                $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    $select,
                    $table,
                    $where_clause,
                    '',
                    $orderBy
                );
        } else {
            $rowArray =
                $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    'uid_foreign cat',
                    $mm_table,
                    $where_clause
                );
        }

        return $rowArray;
    }

    public function getChildCategories($catObject, $cat)
    {
        $tableObj = $catObject->getTableObj();
        $where_clause = $catObject->parentField . ' = ' . intval($cat) . ' ' . $tableObj->enableFields();

        // Fetching the categories
        $rowArray = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid cat', $catObject->getTablename(), $where_clause);

        return $rowArray;
    }

    public function getLineCategories($catObject, $start, $endArray, $where = '')
    {
        $uidArray = [];
        $cat = $start;
        $count = 0;
        if ($cat != '' && count($endArray)) {
            do {
                $uidArray [] = $cat;
                $where_clause = 'uid = ' . intval($cat) . ' ' . $where;
                // Fetching the categories
                $rowArray =
                    $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                        'uid cat, ' . $catObject->parentField . ' uid_parent',
                        $catObject->getTablename(),
                        $where_clause
                    );
                $row = $rowArray[0];
                $cat = $row['uid_parent'];
                $count++;
            } while (
                $cat &&
                !in_array($cat, $endArray) &&
                $count <= 500
            );
        }

        if ($count && $cat) {
            $uidArray[] = $cat;
        }

        return $uidArray;
    }

    public function getCategoryTableContents(
        $catObject,
        $actCatArray,
        $pid,
        $whereClause,
        $groupBy,
        $orderBy,
        $limit,
        &$lineCatArray
    ) {
        $res =
            $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                '*',
                $catObject->getTablename(),
                'parent_category=0',
                $groupBy,
                $orderBy,
                $limit
            );
        $rowArray = [];
        $rootArray = [];
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $rowArray[$row['uid']] = $row;
            $rootArray[] = $row['uid'];
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);
        $lineCatArray =
            $this->getLineCategories(
                $catObject,
                current($actCatArray),
                $rootArray
            );

        if (
            isset($lineCatArray) &&
            is_array($lineCatArray) &&
            count($lineCatArray)
        ) {
            krsort($lineCatArray);
            foreach ($lineCatArray as $cat) {
                $res =
                    $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                        '*',
                        $catObject->getTablename(),
                        'uid=' . $cat
                    );
                while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                    $rowArray [$row['uid']] = $row;
                }
                $GLOBALS['TYPO3_DB']->sql_free_result($res);

                $childArray = $this->getChildCategories($catObject, $cat);
                $childUidArray = [];
                foreach ($childArray as $k => $childRow) {
                    $childUidArray[] = $childRow['cat'];
                }

                if (count($childUidArray)) {
                    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                        '*',
                        $catObject->getTablename(),
                        'uid IN (' . implode(',', $childUidArray) . ')',
                        $groupBy,
                        $orderBy,
                        $limit
                    );
                    while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                        $rowArray[$row['uid']] = $row;
                    }
                    $GLOBALS['TYPO3_DB']->sql_free_result($res);
                }
            }
        }
        return $rowArray;
    }
}
