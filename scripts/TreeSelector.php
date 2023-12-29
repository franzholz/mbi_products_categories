<?php

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use JambageCom\Div2007\Utility\TableUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use JambageCom\MbiProductsCategories\View\TceFuncTreeView;
/***************************************************************
*  Copyright notice
*
*  (c) 2019 René Fritz <r.fritz@colorcube.de>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
* This function displays a selector with nested categories.
* The original code is borrowed from the extension "Digital Asset Management" (tx_dam) author: René Fritz <r.fritz@colorcube.de>
* Modified by Christian Lang <christian.lang@mbi.de> for tt_products.
*
* @author	René Fritz <r.fritz@colorcube.de>
* @author	Rupert Germann <rupi@gmx.li>
* @author	Christian Lang <christian.lang@mbi.de>
* @author	Franz Holzinger <franz@ttproducts.de>
* @maintainer	Christian Lang <christian.lang@mbi.de>
* @package TYPO3
* @subpackage mbi_products_categories
*/
/**
* deprecated and unused
*
* this class displays a tree selector with nested table categories.
*
*/
class TreeSelector implements SingletonInterface
{
    public $pid_list; // list of allowed page ids

    /**
    * Generation of TCEform elements of the type "select"
    * This will render a selector box element, or possibly a special construction with two selector boxes. That depends on configuration.
    *
    * @param	array	$PA: the parameter array for the current field
    * @param	object	$fobj: Reference to the parent object
    * @return	string	the HTML code for the field
    */
    public function displayCategoryTree($PA, $fobj)
    {
        $extensionKey = 'mbi_products_categories';
        $errorMsg = [];
        $table = $PA['table'];
        $field = $PA['field'];
        $row = $PA['row'];

        // Field configuration from TCA:
        $config = $PA['fieldConf']['config'];
        $TSconfig = BackendUtility::getTCEFORM_TSconfig(
            $table,
            $row
        );
        $wherePid = '';

        if ($config['foreign_table_where'] != '') {
            $wherePid = ' ' . TableUtility::foreign_table_where_query($PA['fieldConf'], $field, $TSconfig);
        }

        $this->pObj = $PA['pObj'];
        $this->pid_list = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['pid_list'];

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['useStoragePid']) {
            $this->pid_list = $TSconfig['_STORAGE_PID'];
        }

        // Field configuration from TCA:
        $config = $PA['fieldConf']['config'];

        // Getting the selector box items from the system
        $selItems = $this->pObj->addSelectOptionsToItemArray(
            $this->pObj->initItemArray($PA['fieldConf']),
            $PA['fieldConf'],
            $this->pObj->setTSconfig($table, $row),
            $field
        );
        $selItems =
            $this->pObj->addItems(
                $selItems,
                $PA['fieldTSConfig']['addItems.']
            );
        #if ($config['itemsProcFunc']) $selItems = $this->pObj->procItems($selItems,$PA['fieldTSConfig']['itemsProcFunc.'],$config,$table,$row,$field);

        // Possibly remove some items:
        $removeItems = GeneralUtility::trimExplode(',', $PA['fieldTSConfig']['removeItems'], 1);

        foreach($selItems as $tk => $p) {
            if (in_array($p[1], $removeItems)) {
                unset($selItems[$tk]);
            } elseif (isset($PA['fieldTSConfig']['altLabels.'][$p[1]])) {
                $selItems[$tk][0] = $this->pObj->sL($PA['fieldTSConfig']['altLabels.'][$p[1]]);
            }

            // Removing doktypes with no access:
            if ($table . '.' . $field == 'pages.doktype') {
                if (!($GLOBALS['BE_USER']->isAdmin() || GeneralUtility::inList($GLOBALS['BE_USER']->groupData['pagetypes_select'], $p[1]))) {
                    unset($selItems[$tk]);
                }
            }
        }

        // Creating the label for the "No Matching Value" entry.
        $nMV_label = isset($PA['fieldTSConfig']['noMatchingValue_label']) ? $this->pObj->sL($PA['fieldTSConfig']['noMatchingValue_label']) : '[ ' . $this->pObj->getLL('l_noMatchingValue') . ' ]';
        $nMV_label = @sprintf($nMV_label, $PA['itemFormElValue']);

        // Prepare some values:
        $maxitems = intval($config['maxitems']);
        $minitems = intval($config['minitems']);
        $size = intval($config['size']);
        // If a SINGLE selector box...
        if ($maxitems <= 1 and !$config['treeView']) {
        } else {
            $item = '';
            if (
                $row['sys_language_uid'] &&
                $row['l18n_parent']
            ) { // the current record is a translation of another record

                if ($this->pid_list) {
                    $SPaddWhere = ' AND ' . $config['foreign_table' ] . '.pid IN (' . $this->pid_list . ')';
                }
                $notAllowedItems = [];

                if (
                    $GLOBALS['BE_USER']->getTSConfigVal('options.useListOfAllowedItems') &&
                    !$GLOBALS['BE_USER']->isAdmin()
                ) {
                    $notAllowedItems = $this->getNotAllowedItems($PA, $SPaddWhere . $wherePid);
                }

                // get categories of the translation original
                $catres = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
                    $config['foreign_table'] . '.uid,' . $config['foreign_table'] . '.title,' . $config['MM'] . '.sorting AS mmsorting',
                    $table,
                    $config['MM'],
                    $config['foreign_table'],
                    ' AND ' . $config['MM'] . 'uid_local=' . $row['l18n_parent'] . $SPaddWhere . $wherePid,
                    '',
                    'mmsorting'
                );
                $categories = [];
                $NACats = [];
                $na = false;
                while ($catrow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($catres)) {

                    if(in_array($catrow['uid'], $notAllowedItems)) {
                        $categories[$catrow['uid']] = $NACats[] = '<p style="padding:0px;color:red;font-weight:bold;">- ' . $catrow['title'] . ' <span class="typo3-dimmed"><em>[' . $catrow['uid'] . ']</em></span></p>';
                        $na = true;
                    } else {
                        $categories[$catrow['uid']] = '<p style="padding:0px;">- ' . $catrow['title'] . ' <span class="typo3-dimmed"><em>[' . $catrow['uid'] . ']</em></span></p>';
                    }
                }

                if($na) {
                    $this->NA_Items = '<table class="warningbox" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><img src="gfx/icon_fatalerror.gif" class="absmiddle" alt="" height="16" width="18">SAVING DISABLED!! <br />' . ($row['l18n_parent'] && $row['sys_language_uid'] ? 'The translation original of this' : 'This') . ' record has the following categories assigned that are not defined in your BE usergroup: ' . implode($NACats, chr(10)) . '</td></tr></tbody></table>';
                }
                $item = implode($categories, chr(10));

                if ($item) {
                    $item = 'Categories from the translation original of this record:<br />' . $item;
                } else {
                    $item = 'The translation original of this record has no categories assigned.<br />';
                }
                $item = '<div class="typo3-TCEforms-originalLanguageValue">' . $item . '</div>';
            } else { // build tree selector
                $item .= '<input type="hidden" name="' . $PA['itemFormElName'] . '_mul" value="' . ($config['multiple'] ? 1 : 0) . '" />';

                // Set max and min items:
                $maxitems = MathUtility::forceIntegerInRange($config['maxitems'], 0);
                if (!$maxitems) {
                    $maxitems = 100000;
                }

                $minitems = MathUtility::forceIntegerInRange($config['minitems'], 0);

                // Register the required number of elements:
                $this->pObj->requiredElements[$PA['itemFormElName']] = [$minitems, $maxitems, 'imgName' => $table . '_' . $row['uid'] . '_' . $field];

                //              $errorMsg = [$minitems, $maxitems, 'imgName' => $table . '_' . $row['uid'] . '_' . $field];

                if($config['treeView'] && $config['foreign_table']) {
                    if ($this->pid_list) {
                        $SPaddWhere = ' AND ' . $config['foreign_table'] . '.pid IN (' . $this->pid_list . ')';
                    }

                    if (
                        $GLOBALS['BE_USER']->getTSConfigVal('options.useListOfAllowedItems') &&
                        !$GLOBALS['BE_USER']->isAdmin()
                    ) {
                        $notAllowedItems = $this->getNotAllowedItems($PA, $SPaddWhere . $wherePid);
                    }

                    if(
                        $config['treeViewClass'] &&
                        is_object(
                            $treeViewObj =
                            GeneralUtility::makeInstance(
                                $config['treeViewClass']
                            )
                        )
                    ) {
                    } else {
                        $treeViewObj = GeneralUtility::makeInstance(TceFuncTreeView::class);
                    }
                    $treeViewObj->table = $config['foreign_table'];
                    $treeViewObj->addField('pid'); // neu
                    $treeViewObj->addField('subtitle');  // those fields will be filled to the array $treeViewObj->tree
                    $treeViewObj->addField('catid');
                    $treeViewObj->init($SPaddWhere . $wherePid);
                    $treeViewObj->backPath = $this->pObj->backPath;
                    $treeViewObj->parentField = 'parent_category';

                    $treeViewObj->expandAll = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['expandAll'];
                    $treeViewObj->expandFirst = 1;
                    $treeViewObj->ext_IconMode = '1'; // no context menu on icons
                    $treeViewObj->title = $GLOBALS['LANG']->sL($GLOBALS['TCA'][$config['foreign_table']]['ctrl']['title']);
                    $treeViewObj->TCEforms_itemFormElName = $PA['itemFormElName'];
                    if ($table == $config['foreign_table']) {
                        $treeViewObj->TCEforms_nonSelectableItemsArray[] = $row['uid'];
                    }

                    if (is_array($notAllowedItems) && $notAllowedItems[0]) {
                        foreach ($notAllowedItems as $k) {
                            $treeViewObj->TCEforms_nonSelectableItemsArray[] = $k;
                        }
                    }

                    $treeViewObj->clause = ($this->pid_list ? ' AND ' . $config['foreign_table'] . '.pid IN (' . $this->pid_list . ') ' : '') . $wherePid;

                    // $treeViewObj->orderByFields = str_replace('ORDER BY ','', $GLOBALS['TCA'][$config['foreign_table']]['ctrl']['default_sortby']);

                    // get default items
                    $defItems = [];
                    if (
                        $table == 'tt_content' &&
                        isset($config['items']) &&
                        is_array($config['items']) &&
                        $row['CType'] == 'list' &&
                        in_array($row['list_type'], [5, 9]) &&
                        $field == 'pi_flexform'
                    ) {
                        foreach($config['items'] as $itemValue) {
                            if ($itemValue[0]) {
                                $ITitle = $this->pObj->sL($itemValue[0]);
                                $defItems[] = '<a href="#" onclick="setFormValueFromBrowseWin(\'data[' . $table . '][' . $row['uid'] . '][' . $field . '][data][sDEF][lDEF][categorySelection][vDEF]\',' . $itemValue[1] . ',\'' . $ITitle . '\'); return FALSE;" style="text-decoration:none;">' . $ITitle . '</a>';
                            }
                        }
                    }

                    // render tree html
                    $treeContent = $treeViewObj->getBrowsableTree();
                    $treeItemC = count($treeViewObj->ids);

                    if (isset($defItems['0'])) { // add default items to the tree table. In this case the value [not categorized]
                        $treeItemC += count($defItems);
                        $treeContent .= '<table border="0" cellpadding="0" cellspacing="0"><tr>
                            <td>'.$this->pObj->sL($config['itemsHeader']).'&nbsp;</td><td>' . implode($defItems, '<br />') . '</td>
                            </tr></table>';
                    }

                    // find recursive categories or "storagePid" related errors and if there are some, add a message to the $errorMsg array.
                    $errorMsg = $this->findRecursiveCategories($PA, $row, $table, $storagePid, $treeViewObj->ids);

                    $width = ($config['width'] ?: 280); // default width for the field with the category tree
                    /* 					if (intval($confArr['categoryTreeWidth'])) { // if a value is set in extConf take this one. */
                    /* 						$width = \TYPO3\CMS\Core\Utility\MathUtility::forceIntegerInRange($confArr['categoryTreeWidth'], 1, 600);   */
                    /* 					} else */
                    if ($GLOBALS['CLIENT']['BROWSER'] == 'msie') { // to suppress the unneeded horizontal scrollbar IE needs a width of at least 320px
                        $width = 320;
                    }

                    $config['autoSizeMax'] = MathUtility::forceIntegerInRange($config['autoSizeMax'], 0);
                    $height = $config['autoSizeMax'] ? MathUtility::forceIntegerInRange($treeItemC + 2, MathUtility::forceIntegerInRange($size, 1), $config['autoSizeMax']) : $size;

                    // hardcoded: 16 is the height of the icons
                    $height = $height * 16;

                    $divStyle = 'position:relative; left:0px; top:0px; height:' . $height . 'px; width:' . $width . 'px;border:solid 1px;overflow:auto;background:#fff;margin-bottom:5px;';
                    $thumbnails = '<div  name="' . $PA['itemFormElName'] . '_selTree" style="' . htmlspecialchars($divStyle) . '">';
                    $thumbnails .= $treeContent;
                    $thumbnails .= '</div>';
                } else {
                    $sOnChange = 'setFormValueFromBrowseWin(\'' . $PA['itemFormElName'] . '\',this.options[this.selectedIndex].value,this.options[this.selectedIndex].text); ' . implode('', $PA['fieldChangeFunc']);

                    // Put together the select form with selected elements:
                    $selector_itemListStyle = isset($config['itemListStyle']) ? ' style="' . htmlspecialchars($config['itemListStyle']) . '"' : ' style="' . $this->pObj->defaultMultipleSelectorStyle . '"';

                    $size = $config['autoSizeMax'] ? MathUtility::forceIntegerInRange(count($itemArray) + 1, MathUtility::forceIntegerInRange($size, 1), $config['autoSizeMax']) : $size;

                    $thumbnails = '<select style="width:150px;" name="' . $PA['itemFormElName'] . '_sel"' . $this->pObj->insertDefStyle('select') . ($size ? ' size="' . $size . '"' : '') . ' onchange="' . htmlspecialchars($sOnChange) . '"' . $PA['onFocus'] . $selector_itemListStyle . '>';
                    #$thumbnails = '<select                       name="'.$PA['itemFormElName'].'_sel"'.$this->pObj->insertDefStyle('select').($size?' size="'.$size.'"':'').' onchange="'.htmlspecialchars($sOnChange).'"'.$PA['onFocus'].$selector_itemListStyle.'>';
                    foreach($selItems as $p) {
                        $thumbnails .= '<option value="' . htmlspecialchars($p[1]) . '">' . htmlspecialchars($p[0]) . '</option>';
                    }
                    $thumbnails .= '</select>';
                }

                // Perform modification of the selected items array:
                $itemArray = GeneralUtility::trimExplode(',', $PA['itemFormElValue'], 1);

                foreach($itemArray as $tk => $tv) {
                    $tvP = explode('|', $tv, 2);
                    if (
                        in_array($tvP[0], $removeItems) &&
                        !$PA['fieldTSConfig']['disableNoMatchingValueElement']
                    ) {
                        $tvP[1] = rawurlencode($nMV_label);
                    } elseif (isset($PA['fieldTSConfig']['altLabels.'][$tvP[0]])) {
                        $tvP[1] = rawurlencode($this->pObj->sL($PA['fieldTSConfig']['altLabels.'][$tvP[0]]));
                    } else {
                        $tvP[1] = rawurlencode($this->pObj->sL(rawurldecode($tvP[1])));
                    }
                    $record = $treeViewObj->getRecord(intval($tvP['0']));
                    $catid = '';
                    if ($record['catid']) {
                        $catid = ' - ' . $record['catid'];
                    }
                    $itemArray[$tk] = $tvP['0'] . '|' . $tvP['1'] . $catid . ' - ' . $tvP['0'];
                }

                $sWidth = 150; // default width for the left field of the category select
                /* 				if (intval($confArr['categorySelectedWidth'])) { */
                /* 					$sWidth = \TYPO3\CMS\Core\Utility\MathUtility::forceIntegerInRange($confArr['categorySelectedWidth'], 1, 600); */
                /* 				} */
                $params = [
                    'size' => $size,
                    'autoSizeMax' => MathUtility::forceIntegerInRange($config['autoSizeMax'], 0),
                    #'style' => isset($config['selectedListStyle']) ? ' style="'.htmlspecialchars($config['selectedListStyle']).'"' : ' style="'.$this->pObj->defaultMultipleSelectorStyle.'"',
                    'style' => ' style="width:' . $sWidth . 'px;"',
                    'dontShowMoveIcons' => ($maxitems <= 1),
                    'maxitems' => $maxitems,
                    'info' => '',
                    'headers' => [
                        'selector' => $this->pObj->getLL('l_selected') . ':<br />',
                        'items' => $this->pObj->getLL('l_items') . ':<br />'
                    ],
                    'noBrowser' => 1,
                    'thumbnails' => $thumbnails
                ];

                $item .=
                    $this->pObj->dbFileIcons(
                        $PA['itemFormElName'],
                        '',
                        '',
                        $itemArray,
                        '',
                        $params,
                        $PA['onFocus']
                    );

                // Wizards:
                $altItem = '<input type="hidden" name="' . $PA['itemFormElName'] . '" value="' . htmlspecialchars($PA['itemFormElValue']) . '" />';
                $item = $this->pObj->renderWizards(
                    [$item,$altItem],
                    $config['wizards'],
                    $table,
                    $row,
                    $field,
                    $PA,
                    $PA['itemFormElName'],
                    $specConf
                );
            }
        }

        $result = $this->NA_Items.implode($errorMsg, chr(10)) . $item;
        return $result;
    }


    /**
    * This function checks if there are categories selectable that are not allowed for this BE user and if the current record has
    * already categories assigned that are not allowed.
    * If such categories were found they will be returned and "$this->NA_Items" is filled with an error message.
    * The array "$itemArr" which will be returned contains the list of all non-selectable categories. This array will be added to "$treeViewObj->TCEforms_nonSelectableItemsArray". If a category is in this array the "select item" link will not be added to it.
    *
    * @param	array		$PA: the paramter array
    * @param	string		$SPaddWhere: this string is added to the query for categories when "useStoragePid" is set.
    * @return	array		array with not allowed categories
    * @see tx_ttnews_tceFunc_selectTreeView::wrapTitle()
    */
    public function getNotAllowedItems($PA, $SPaddWhere = '')
    {
        $fTable = $PA['fieldConf']['config']['foreign_table'];
        // get list of allowed categories for the current BE user
        $allowedItemsList = $GLOBALS['BE_USER']->getTSConfigVal('tt_productsPerms.' . $fTable . '.allowedItems');

        $itemArr = [];
        if ($allowedItemsList) {
            // get all categories
            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', $fTable, '1=1' . $SPaddWhere . ' AND NOT deleted');
            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                if(
                    !GeneralUtility::inList($allowedItemsList, $row['uid'])
                ) { // remove all allowed categories from the category result
                    $itemArr[] = $row['uid'];
                }
            }
            if (!$PA['row']['sys_language_uid'] && !$PA['row']['l18n_parent']) {
                $catvals = explode(',', $PA['row']['category']); // get categories from the current record
                $notAllowedCats = [];
                foreach ($catvals as $k) {
                    $c = explode('|', $k);
                    if($c[0] && !GeneralUtility::inList($allowedItemsList, $c[0])) {
                        $notAllowedCats[] = '<p style="padding:0px;color:red;font-weight:bold;">- ' . $c[1] . ' <span class="typo3-dimmed"><em>[' . $c[0] . ']</em></span></p>';
                    }
                }
                if ($notAllowedCats[0]) {
                    $this->NA_Items = '<table class="warningbox" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><img src="gfx/icon_fatalerror.gif" class="absmiddle" alt="" height="16" width="18">SAVING DISABLED!! <br />This record has the following categories assigned that are not defined in your BE usergroup: '.implode($notAllowedCats, chr(10)).'</td></tr></tbody></table>';
                }
            }
        }

        return $itemArr;
    }

    /**
    * detects recursive categories and returns an error message if recursive categories where found
    *
    * @param	array		$PA: the paramter array
    * @param	array		$row: the current row
    * @param	array		$table: current table
    * @param	integer		$storagePid: the StoragePid (pid of the category folder)
    * @param	array		$treeIds: array with the ids of the categories in the tree
    * @return	array		error messages
    */
    public function findRecursiveCategories($PA, $row, $table, $storagePid, $treeIds)
    {
        // Field configuration from TCA:
        $config = $PA['fieldConf']['config'];

        $errorMsg = [];

        if (
            $table == 'tt_content' &&
            $row['CType'] == 'list'
        ) { // = tt_content element which inserts plugin of the extension

            if (
                in_array(
                    $row['list_type'],
                    ['5', '9', 'tt_products_pi_int']
                )
            ) {
                $cfgArr = GeneralUtility::xml2array($row['pi_flexform']);

                if (
                    is_array($cfgArr) &&
                    isset($cfgArr['data']) &&
                    is_array($cfgArr['data']) &&
                    isset($cfgArr['data']['sDEF']) &&
                    is_array($cfgArr['data']['sDEF']) &&
                    isset($cfgArr['data']['sDEF']['lDEF']) &&
                    is_array($cfgArr['data']['sDEF']['lDEF']) &&
                    $cfgArr['data']['sDEF']['lDEF']['categorySelection']
                ) {
                    $rcList =
                        $this->compareCategoryVals(
                            $treeIds,
                            $cfgArr['data']['sDEF']['lDEF']['categorySelection']['vDEF']
                        );
                }
            }
        } elseif (
            $table == $config['foreign_table'] ||
            $table == $PA['table']
        ) {
            if (
                $table == $config['foreign_table'] &&
                $row['pid'] == $storagePid &&
                intval($row['uid']) &&
                !in_array($row['uid'], $treeIds)
            ) { // if the selected category is not empty and not in the array of tree-uids it seems to be part of a chain of recursive categories
                $recursionMsg = 'MISSING CATEGORIES DETECTED!!  <br />The category of this record is missing in the tree categories. You should remove the parent category of this record to prevent this.';
            }

            if (
                $table == $PA['table'] &&
                $row[$PA['field']]
            ) { // find recursive categories in the extension's db-record
                $rcList = $this->compareCategoryVals($treeIds, $row[$PA['field']]);
            }

            // in case of localized records this doesn't work
            if (
                $storagePid &&
                $row['pid'] != $storagePid &&
                $table == $config['foreign_table']
            ) { // if a storagePid is defined but the current category is not stored in storagePid
                $errorMsg[] = '<p style="padding:10px;"><img src="gfx/icon_warning.gif" class="absmiddle" alt="" height="16" width="18"><strong style="color:red;"> Warning:</strong><br />The extension using table \''.$PA['table'].'\' is configured to display categories only from the "General record storage page" (GRSP). The current category is not located in the GRSP and will so not be displayed. To solve this you should either define a GRSP or disable "Use StoragePid" in the extension manager.</p>';
            }
        }

        if (strlen($rcList)) {
            $recursionMsg = 'RECURSIVE OR UNALLOWED CATEGORIES DETECTED!! <br />This record has the following recursive categories assigned: '.$rcList.'<br />Recursive categories will not be shown in the category tree and will therefore not be selectable. ';

            if ($table == $PA['table']) {
                $recursionMsg .= 'To solve this problem mark these categories in the left select field, click on "edit category" and clear the field "parent category" of the recursive category. If you are not an admin user, then make sure to get the correct MOUNT to the folder which contains the products categories records. See bug #63047';
            } else {
                $recursionMsg .= 'To solve this problem you should clear the field "parent category" of the recursive category.';
            }
        }

        if ($recursionMsg) {
            $errorMsg[] = '<table class="warningbox" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><img src = "gfx/icon_fatalerror.gif" class="absmiddle" alt="" height="16" width="18">' . $recursionMsg . '</td></tr></tbody></table>';
        }

        return $errorMsg;
    }

    /**
    * This function compares the selected categories ($catString) with the categories from the category tree ($treeIds).
    * If there are categories selected that are not present in the array $treeIds it assumes that those categories are
    * parts of a chain of recursive categories returns their uids.
    *
    * @param	array		$treeIds: array with the ids of the categories in the tree
    * @param	string		$catString: the selected categories in a string (format: uid|title,uid|title,...)
    * @return	string		list of recursive categories
    */
    public function compareCategoryVals($treeIds, $catString)
    {
        $recursiveCategories = [];
        $showncats = implode($treeIds, ','); // the displayed categories (tree)
        $catvals = explode(',', $catString); // categories of the current record (left field)

        foreach ($catvals as $k) {
            $c = explode('|', $k);
            if(!GeneralUtility::inList($showncats, $c[0])) {
                $recursiveCategories[] = $c;
            }
        }

        if ($recursiveCategories[0]) {
            $rcArr = [];
            foreach ($recursiveCategories as $cat) {
                if ($cat[0]) {
                    $rcArr[] = $cat[1] . ' (' . $cat[0] . ')'; // format result: title (uid)
                }
            }
            $rcList = implode($rcArr, ', ');
        }

        return $rcList;
    }

    /**
    * This functions displays the title field of a products record and checks if the record has categories assigned that are not allowed for the current BE user.
    * If there are non allowed categories an error message will be displayed.
    *
    * @param	array		$PA: the parameter array for the current field
    * @param	object		$fobj: Reference to the parent object
    * @return	string		the HTML code for the field and the error message
    */
    public function displayTypeFieldCheckCategories(&$PA, $fobj)
    {
        $SPaddWhere = '';
        $config = $PA['fieldConf']['config'];
        $table = $PA['table'];
        $field = $PA['field'];
        $row = $PA['row'];

        if (
            $GLOBALS['BE_USER']->getTSConfigVal('options.useListOfAllowedItems') &&
            !$GLOBALS['BE_USER']->isAdmin()
        ) {
            $notAllowedItems = [];
            if ($this->pid_list) {
                $SPaddWhere = ' AND ' . $config['foreign_table'] . '.pid IN (' . $this->pid_list . ')';
            }
            $notAllowedItems = $this->getNotAllowedItems($PA, $SPaddWhere);

            if ($notAllowedItems[0]) {
                // get categories of the record in db
                $uidField = $row['l18n_parent'] && $row['sys_language_uid'] ? $row['l18n_parent'] : $row['uid'];
                $catres = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
                    $config['foreign_table'] . '.uid,' . $config['foreign_table'] . '.title,' . $config['MM'] . '.sorting AS mmsorting',
                    $table,
                    $config['MM'],
                    $config['foreign_table'],
                    ' AND ' . $config['MM'] . '.uid_local=' . $uidField . $SPaddWhere,
                    '',
                    'mmsorting'
                );

                $NACats = [];
                while ($catrow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($catres)) {
                    if($catrow['uid'] && $notAllowedItems[0] && in_array($catrow['uid'], $notAllowedItems)) {
                        $NACats[] = '<p style="padding:0px;color:red;font-weight:bold;">- ' . $catrow['title'] . ' <span class="typo3-dimmed"><em>[' . $catrow['uid'] . ']</em></span></p>';
                    }
                }
                if($NACats[0]) {
                    $NA_Items =  '<table class="warningbox" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><img src="gfx/icon_fatalerror.gif" class="absmiddle" alt="" height="16" width="18">SAVING DISABLED!! <br />'.($row['l18n_parent'] && $row['sys_language_uid'] ? 'The translation original of this' : 'This').' record has the following categories assigned that are not defined in your BE usergroup: ' . implode($NACats, chr(10)) . '</td></tr></tbody></table>';
                }
            }
        }
        // unset foreign table to prevent adding of categories to the "type" field
        $PA['fieldConf']['config']['foreign_table'] = '';
        $PA['fieldConf']['config']['foreign_table_where'] = '';
        if (!$row['l18n_parent'] && !$row['sys_language_uid']) { // render "type" field only for records in the default language
            $fieldHTML = $fobj->getSingleField_typeSelect($table, $field, $row, $PA);
        }
        return $NA_Items . $fieldHTML;
    }
}
