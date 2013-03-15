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
 * @package admin
 * @copyright (C) OXID eSales AG 2003-2009
 * @version OXID eShop CE
 * $Id: voucherserie_groups.php 16302 2009-02-05 10:18:49Z rimvydas.paskevicius $
 */

/**
 * Admin voucherserie groups manager.
 * Collects and manages information about user groups, added to one or another
 * serie of vouchers.
 * Admin Menu: Shop Settings -> Vouchers -> Groups.
 * @package admin
 */
class VoucherSerie_Groups extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxlist and oxvoucherserie
     * objects, passes it's data to Smarty engine and returns name of template
     * file "voucherserie_groups.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = oxConfig::getParameter( "oxid");
        // check if we right now saved a new entry

        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oVoucherSerie = oxNew( "oxvoucherserie" );
            $oVoucherSerie->load( $soxId);
            $oVoucherSerie->setUserGroups();
            $this->_aViewData["edit"] =  $oVoucherSerie;

            //Disable editing for derived items
            if ($oVoucherSerie->isDerived())
                $this->_aViewData['readonly'] = true;
        }
        if ( oxConfig::getParameter("aoc") ) {

            $aColumns = array();
            include_once 'inc/'.strtolower(__CLASS__).'.inc.php';
            $this->_aViewData['oxajax'] = $aColumns;

            return "popups/voucherserie_groups.tpl";
        }

        return "voucherserie_groups.tpl";
    }
}
