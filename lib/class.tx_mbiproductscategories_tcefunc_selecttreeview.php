<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Rupert Germann <rupi@gmx.li>
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
 * $Id$
 *
 * @author	Rupert Germann <rupi@gmx.li>
 * @author	Christian Lang <christian.lang@mbi.de>
 * @author	Franz Holzinger <kontakt@fholzinger.com>
 * @package TYPO3
 * @subpackage mbi_products_categories
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   58: class tx_ttnews_tceFunc_selectTreeView extends t3lib_treeview
 *   70:     function wrapTitle($title,$v)
 *
 *
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_t3lib.'class.t3lib_treeview.php');


	/**
	 * extend class t3lib_treeview to change function wrapTitle().
	 *
	 */
class tx_mbiproductscategories_tceFunc_selectTreeView extends t3lib_treeview {

	var $TCEforms_itemFormElName='';
	var $TCEforms_nonSelectableItemsArray=array();

	/**
	 * wraps the record titles in the tree with links or not depending on if they are in the TCEforms_nonSelectableItemsArray.
	 *
	 * @param	string		$title: the title
	 * @param	array		$v: an array with uid and title of the current item.
	 * @return	string		the wrapped title
	 */
	function wrapTitle($title,$v)	{
		if($v['uid']>0) {
			if (in_array($v['subtitle'],$this->TCEforms_nonSelectableItemsArray)) {
				return '<a href="#" title="'.$v['title'].'"><span style="color:#999;cursor:default;">'.$title.'</span></a>';
			} else {
				$hrefTitle = $v['subtitle'];
				$aOnClick = 'setFormValueFromBrowseWin(\''.$this->TCEforms_itemFormElName.'\','.$v['uid'].',\''.$title.'\'); return false;';
				return '<a href="#" onclick="'.htmlspecialchars($aOnClick).'" title="'.htmlentities($v['subtitle']).'">'.$title.'</a>';
			}
		} else {
			return $title;
		}
	}

	/**
	 * Wrapping the image tag, $icon, for the row, $row (except for mount points)
	 *
	 * @param	string		The image tag for the icon
	 * @param	array		The row for the current element
	 * @return	string		The processed icon input value.
	 * @access private
	 */
 	function wrapIcon($icon,$row)	{
 		$theIcon='<a href="#" title="id='.$row['uid'].'">'.$icon.'</a>';
		return $theIcon;
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mbi_products_categories/class.tx_mbiproductscategories_tcefunc_selecttreeview.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mbi_products_categories/class.tx_mbiproductscategories_tcefunc_selecttreeview.php']);
}
?>
