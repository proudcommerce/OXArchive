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
 * @package inc
 * @copyright (C) OXID eSales AG 2003-2009
 * @version OXID eShop CE
 */

$aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxtitle',  'oxcategories', 1, 1, 0 ),
                                        array( 'oxdesc',   'oxcategories', 1, 1, 0 ),
                                        array( 'oxid',     'oxcategories', 0, 0, 1 )
                                        )
                    );
/**
 * Class controls article assignment to attributes
 */
class ajaxComponent extends ajaxListComponent
{
    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oCat = oxNew('oxcategory');

        $sCategoriesTable = $oCat->getViewName();

        $sSynchOxid = oxConfig::getParameter( 'synchoxid' );

        $sQAdd  = " from $sCategoriesTable where ".$oCat->getSqlActiveSnippet();

        return $sQAdd;
    }

    /**
     * Removing article from corssselling list
     *
     * @return null
     */
    public function unassignCat()
    {
        $sShopId = oxConfig::getParameter( 'oxid');
        $oShop = oxNew('oxshop');
        if ($oShop->load($sShopId)) {
            $oShop->oxshops__oxdefcat = new oxField('');
            $oShop->save();
        }
    }

    /**
     * Adding article to corssselling list
     *
     * @return null
     */
    public function assignCat()
    {
        $sChosenCat = oxConfig::getParameter( 'oxcatid' );
        $sShopId    = oxConfig::getParameter( 'oxid');
        $oShop = oxNew('oxshop');
        if ($oShop->load($sShopId)) {
            $oShop->oxshops__oxdefcat = new oxField($sChosenCat);
            $oShop->save();
        }
    }
}
