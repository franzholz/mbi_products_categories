<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Sami Ben-yahia (sittinggoat@hotmail.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
 * Plugin 'cat2menu' for the 'nsb_cat2menu' extension.
 *
 * @author	Sami Ben-yahia <sittinggoat@hotmail.com>
 * @author	Michael Hoppe <michael@hoppefamily.de>
 * @author	Franz Holzinger <franz@ttproducts.de>
 */



class tx_nsbcat2menu_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin implements \TYPO3\CMS\Core\SingletonInterface
{
    //	public $prefixId = 'tx_nsbcat2menu_pi1';		// Same as class name (used only by pivars ?)
    public $scriptRelPath = 'pi1/class.tx_nsbcat2menu_pi1.php';	// Path to this script relative to the extension dir.
    public $extKey = 'nsb_cat2menu';	// The extension key.
    //i must think
    public $pi_checkCHash = true;
    public $internal = [		// Used internally for general storage of values between methods
        'catArr' => [],		//Current category table from pi_getCategoryTableContents
        'recSelReg' => ''	//Used only if the recursive select option is on (recursiveSelectionRegistry)
    ];
    public $tableObj;

    public function getTableObj()
    {
        return $this->tableObj;
    }

    public function getTablename()
    {
        return 'tx_mbiproductscategories_mm';
    }

    public function main($content, $conf)
    {
        $this->conf = $conf;
        // I don't expect side effects
        $this->prefixId = $this->conf['extTrigger'];
        $my_vars = $GLOBALS['TSFE']->fe_user->getKey('ses', 'nsb_cat2menu');
        $tmpAct = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET($this->prefixId);
        $tmpAct = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $tmpAct['cat']);

        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('table')) {
            $this->tableObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_table_db');
        }

        if(0) {
            //		if(isset($my_vars[$this->prefixId])){
            $menuArray = $my_vars[$this->prefixId];
            $this->markActive($menuArray, $tmpAct);
        } else {
            $this->conf['targetId'] = $this->conf['targetId'] ? $this->conf['targetId'] : 0;
            $table = $this->conf['catTable'];
            $pid = $this->conf['pidlist'];
            //get the whole autorized category table, the tree will be constructed later in php with makeMenuArray($rootLine)
            $whereClause = '';
            $orderBy = '';
            $limit = '';
            $mbiObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\JambageCom\MbiProductsCategories\Utility\Category::class);

            $lineCatArray = [];
            $this->internal['catArr'] =
                $mbiObj->getCategoryTableContents(
                    $this,
                    $tmpAct,
                    $table,
                    $pid,
                    $whereClause,
                    $groupBy,
                    $orderBy,
                    $limit,
                    $lineCatArray
                );
            foreach ($lineCatArray as $value) {
                $this->internal['catArr'][$value]['ITEM_STATE'] = 'ACT';
            }
            //mark active for no cookie client config

            foreach ($tmpAct as $value) {
                $this->internal['catArr'][$value]['ITEM_STATE'] = 'CUR';
            }

            $menuArray = $this->makeMenuArray(\TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $this->conf['catList']));
            //this session storage alows different multiple instances only if the triggered extension is different
            $my_vars[$this->prefixId] = $menuArray;
            $GLOBALS["TSFE"]->fe_user->setKey('ses', 'nsb_cat2menu', $my_vars);
        }
        $rc = $this->lightenMenu($menuArray);
        return $rc;
    }
    //used by the session menuArray
    public function markActive(&$menuArray, $actCatArr)
    {
        while(current($actCatArr)) {
            reset($menuArray);
            while (list($key, $val) = each($menuArray)) {
                if(current($actCatArr) == $menuArray[$key]['uid']) {
                    $menuArray[$key]['ITEM_STATE' ] = 'ACT';
                }
                if($menuArray[$key]['_SUB_MENU']) {
                    $this->markActive($menuArray[$key]['_SUB_MENU'], $actCatArr);
                }
            }
            next($actCatArr);
        }
    }
    //currently will get all the branches from the category 'rootline' no recursive level option
    public function makeMenuArray($rootLine)
    {

        //autodetect catList
        if($rootLine[0] == 0) {
            $i = 0;
            foreach($this->internal['catArr'] as $t) {
                if($t['parent_category'] == '0') {
                    $rootLine[$i] = $t['uid'] + 0;
                    $i++;
                }
            }
        }

        foreach($rootLine as $k => $v) {
            $menuArray[$k] = $this->internal['catArr'][$v];
            $this->getHref($menuArray[$k]);

            if ($menuArray[$k]['ITEM_STATE'] == 'ACT' || $menuArray[$k]['ITEM_STATE'] == 'CUR') {
                $act = 1;
            } else {
                $act = 0;
            }
            $this->makeSubMenu($menuArray[$k], $act);
        }
        return $menuArray;
    }

    public function makeSubMenu(&$menuArray, $act)
    {
        foreach($this->internal['catArr'] as $v) {
            if($menuArray['uid'] == $v[$this->conf['parentEntry']]) {
                $this->getHref($v);

                if($v['ITEM_STATE'] == 'ACT' || $v['ITEM_STATE'] == 'CUR') {
                    $v['ROOTLINE'] = true;
                    $ret = '1';
                } else {
                    $ret = '0';
                }
                $retc = $this->makeSubMenu($v, $ret);
                if($retc) {
                    $v['ROOTLINE'] = true;
                }
                if($retc or $ret or ($act == '1')) {
                    $menuArray['_SUB_MENU'][] = $v;
                }
            }
        }

        return $ret;
    }

    public function getHref(&$menuArray)
    {
        if($this->conf['recSel']) {
            $this->internal['recSelreg'] = $menuArray['uid'];
            $this->getRecHref($menuArray);
            //TODO this->conf['varHasCHash']
            $menuArray['_OVERRIDE_HREF'] = $this->pi_linkTP_keepPIvars_url([$this->conf['varCat'] => $this->internal['recSelreg']], 0, 0, $this->conf['targetId']);
        } else {
            $menuArray['_OVERRIDE_HREF'] = $this->pi_linkTP_keepPIvars_url([$this->conf['varCat'] => $menuArray['uid']], 0, 0, $this->conf['targetId']);
        }
    }

    public function getRecHref($menuArray)
    {
        foreach($this->internal['catArr'] as $v) {
            if($menuArray['uid'] == $v[$this->conf['parentEntry']]) {
                $this->internal['recSelreg'] .= ',' . $v['uid'];
                $this->getRecHref($v);
            }
        }
    }

    //used to avoid side effects by removing unecessary keys
    public function lightenMenu($menuArray)
    {

        foreach($menuArray as $key => $val) {
            $lightMenuArray[$key]['title'] = $menuArray[$key]['title'];
            $lightMenuArray[$key]['_OVERRIDE_HREF'] = $menuArray[$key]['_OVERRIDE_HREF'];
            $lightMenuArray[$key]['ITEM_STATE'] = $menuArray[$key]['ITEM_STATE'];
            if($menuArray[$key]['_SUB_MENU']) {
                $this->lightenSubMenu($lightMenuArray[$key]['_SUB_MENU'], $menuArray[$key]['_SUB_MENU']);
            }
        }
        return $lightMenuArray;
    }

    public function lightenSubMenu(&$lightMenuArray, $menuArray)
    {
        while (list($key, $val) = each($menuArray)) {
            $lightMenuArray[$key]['title'] = $menuArray[$key]['title'];
            $lightMenuArray[$key]['_OVERRIDE_HREF'] = $menuArray[$key]['_OVERRIDE_HREF'];
            $lightMenuArray[$key]['ITEM_STATE'] = $menuArray[$key]['ITEM_STATE'];
            if($menuArray[$key]['_SUB_MENU']) {
                $this->lightenSubMenu($lightMenuArray[$key]['_SUB_MENU'], $menuArray[$key]['_SUB_MENU']);
            }
        }
    }
}
