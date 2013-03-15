<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.oxid-esales.com
 * @package smartyPlugins
 * @copyright (C) OXID eSales AG 2003-2009
 * @version OXID eShop CE
 * $Id: modifier.oxnumberformat.php 20624 2009-07-03 10:40:53Z vilma $
 */

/**
 * Smarty cat modifier plugin
 *
 * Type:     modifier<br>
 * Name:     smarty_modifier_oxformdate<br>
 * Date:     05/27/2005
 * Purpose:  Formats number for chosen locale
 * Input:    object to convert
 * Example:  {$object|oxformdate:"foo"}
 * @param string $sValue Number to format
 * @param string $sFormat Number formatting rules (use default currency formattin rules defined in Admin)
 * @return string
 */
function smarty_modifier_oxnumberformat( $sFormat = "EUR@ 1.00@ ,@ .@ �@ 2", $sValue) {
    // logic copied from oxconfig::getCurrencyArray()
    $sCur = explode( "@", $sFormat);
    $oCur           = new oxStdClass();
    $oCur->id       = 0;
    $oCur->name     = @trim($sCur[0]);
    $oCur->rate     = @trim($sCur[1]);
    $oCur->dec      = @trim($sCur[2]);
    $oCur->thousand = @trim($sCur[3]);
    $oCur->sign     = @trim($sCur[4]);
    $oCur->decimal  = 0;//trim($sCur[5]);
    // change for US version
    if(isset($sCur[6]))
        $oCur->side = @trim($sCur[6]);

    return oxLang::getInstance()->formatCurrency($sValue, $oCur);
}
