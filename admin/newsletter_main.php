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
 * @link      http://www.oxid-esales.com
 * @package   admin
 * @copyright (C) OXID eSales AG 2003-2011
 * @version OXID eShop CE
 * @version   SVN: $Id: newsletter_main.php 25466 2010-02-01 14:12:07Z alfonsas $
 */

/**
 * Admin article main newsletter manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Customer News -> Newsletter -> Main.
 * @package admin
 */
class Newsletter_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxnewsletter object
     * and passes it's data to Smarty engine. Returns name of template file
     * "newsletter_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = oxConfig::getParameter( "oxid");
        // check if we right now saved a new entry
        $sSavedID = oxConfig::getParameter( "saved_oxid");
        if ( ($soxId == "-1" || !isset( $soxId)) && isset( $sSavedID) ) {
            $soxId = $sSavedID;
            oxSession::deleteVar( "saved_oxid");
            $this->_aViewData["oxid"] =  $soxId;
            // for reloading upper frame
            $this->_aViewData["updatelist"] =  "1";
        }

        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oNewsletter = oxNew( "oxnewsletter" );
            $oNewsletter->load( $soxId);
            $this->_aViewData["edit"] =  $oNewsletter;
        }

        // generate editor
        $this->_aViewData["editor"] = $this->_generateTextEditor( "100%", 255, $oNewsletter, "oxnewsletter__oxtemplate");

        return "newsletter_main.tpl";
    }

    /**
     * Saves newsletter HTML format text.
     *
     * @return string
     */
    public function save()
    {   $myConfig  = $this->getConfig();

        $soxId      = oxConfig::getParameter( "oxid");
        $aParams    = oxConfig::getParameter( "editval");

        // shopid
        $sShopID = oxSession::getVar( "actshop");
        $aParams['oxnewsletter__oxshopid'] = $sShopID;

        $oNewsletter = oxNew( "oxnewsletter" );
        if ( $soxId != "-1")
            $oNewsletter->load( $soxId);
        else
            $aParams['oxnewsletter__oxid'] = null;

        $oNewsletter->assign( $aParams);
        $oNewsletter->save();
        $this->_aViewData["updatelist"] = "1";

        // set oxid if inserted
        if ( $soxId == "-1")
            oxSession::setVar( "saved_oxid", $oNewsletter->oxnewsletter__oxid->value);
    }
}
