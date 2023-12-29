<?php

namespace JambageCom\MbiProductsCategories\View;

use TYPO3\CMS\Backend\Tree\View\AbstractTreeView;
/***************************************************************
*  Copyright notice
*
*  (c) 2006-2019 René Fritz <r.fritz@colorcube.de>
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
*
* @author	René Fritz <r.fritz@colorcube.de>
* @author	Christian Lang <christian.lang@mbi.de>
* @author	Franz Holzinger <franz@ttproducts.de>
* @maintainer	Franz Holzinger <franz@ttproducts.de>
* @package TYPO3
* @subpackage mbi_products_categories
*/
/**
* extend class AbstractTreeView to change function wrapTitle().
*
*/
class TceFuncTreeView extends AbstractTreeView
{
    public $TCEforms_itemFormElName = '';
    public $TCEforms_nonSelectableItemsArray = [];

    /**
    * wraps the record titles in the tree with links or not depending on if they are in the TCEforms_nonSelectableItemsArray.
    *
    * @param	string		$title: the title
    * @param	array		$v: an array with uid and title of the current item.
    * @return	string		the wrapped title
    */
    public function wrapTitle($title, $v)
    {
        if($v['uid'] > 0) {
            if (
                in_array($v['subtitle'], $this->TCEforms_nonSelectableItemsArray)
            ) {
                $result = '<a href="#" title="' . $v['title'] . '"><span style="color:#999;cursor:default;">' . $title . '</span></a>';
            } else {
                $aOnClick = 'setFormValueFromBrowseWin(\'' . $this->TCEforms_itemFormElName . '\',' . $v['uid'] . ',\'' . $title . '\'); return false;';
                $result = '<a href="#" onclick="' . htmlspecialchars($aOnClick) . '" title="id=' . $v['uid'] . ' ' . htmlentities($v['subtitle']) . ' ' . htmlentities($v['catid']) . '">' . $title . '</a>';
            }
        } else {
            $result = $title;
        }
        return $result;
    }

    /**
    * Wrapping the image tag, $icon, for the row, $row (except for mount points)
    *
    * @param	string		The image tag for the icon
    * @param	array		The row for the current element
    * @return	string		The processed icon input value.
    * @access private
    */
    public function wrapIcon($icon, $row)
    {
        $theIcon = '<a href="#">' . $icon . '</a>';
        return $theIcon;
    }
}
