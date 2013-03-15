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
 * $Id: adminguestbook_main.php 17188 2009-03-13 12:19:11Z arvydas $
 */

/**
 * Guestbook record manager.
 * Returns template, that arranges guestbook record information.
 * Admin Menu: User information -> Guestbook -> Main.
 * @package admin
 */
class Adminguestbook_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render() and returns template file
     * name "adminguestbook_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig  = $this->getConfig();

        parent::render();

        $soxId = oxConfig::getParameter( "oxid");

        // check if we right now saved a new entry
        $sSavedID = oxConfig::getParameter( "saved_oxid" );
        if ( ( $soxId == "-1" || !isset( $soxId ) ) && isset( $sSavedID ) ) {

            $soxId = $sSavedID;
            oxSession::deleteVar( 'saved_oxid' );
            $this->_aViewData["oxid"] = $soxId;

            // for reloading upper frame
            $this->_aViewData["updatelist"] = '1';
        }

        if ( $soxId != '-1' && isset( $soxId ) ) {
            // load object
            $oLinks = oxNew( 'oxgbentry' );
            $oLinks->load( $soxId );

            // #580A - setting GB entry as viewed in admin
            if ( !$oLinks->oxgbentries__oxviewed->value ) {
                $oLinks->oxgbentries__oxviewed->setValue(1);
                $oLinks->save();
            }
            $this->_aViewData["edit"] =  $oLinks;
        }

        $this->_aViewData['editor'] = $this->_generateTextEditor( "100%", 300, $oLinks, 'oxgbentries__oxcontent', 'agb.tpl.css' );

        //show "active" checkbox if moderating is active
        $this->_aViewData['blShowActBox'] = $myConfig->getConfigParam( 'blGBModerate' );

        return 'adminguestbook_main.tpl';
    }

    /**
     * Saves guestbook record changes.
     *
     * @return null
     */
    public function save()
    {

        $soxId   = oxConfig::getParameter( "oxid" );
        $aParams = oxConfig::getParameter( "editval" );

        // checkbox handling
        if ( !isset( $aParams['oxgbentries__oxactive'] ) )
            $aParams['oxgbentries__oxactive'] = 0;

            // shopid
            $sShopID = oxSession::getVar( "actshop");
            $aParams['oxgbentries__oxshopid'] = $sShopID;

        $oLinks = oxNew( "oxgbentry" );

        if ( $soxId != "-1" ) {
            $oLinks->load( $soxId );
        } else {
            $aParams['oxgbentries__oxid'] = null;

            // author
            $iUsrID = oxSession::getVar( 'auth' );
            $aParams['oxgbentries__oxuserid'] = $iUsrID;
        }

        $oLinks->assign( $aParams );
        $oLinks->save();
        $this->_aViewData['updatelist'] = '1';

        // set oxid if inserted
        if ( $soxId == '-1' ) {
            oxSession::setVar( 'saved_oxid', $oLinks->oxgbentries__oxid->value );
        }
    }

}
