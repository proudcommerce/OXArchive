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
 * @copyright � OXID eSales AG 2003-2008
 * $Id: user_extend.php 14015 2008-11-06 13:30:18Z arvydas $
 */

/**
 * Admin user extended settings manager.
 * Collects user extended settings, updates it on user submit, etc.
 * Admin Menu: User Administration -> Users -> Extended.
 * @package admin
 */
class User_Extend extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxuser object and
     * returns name of template file "user_extend.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = oxConfig::getParameter( "oxid");
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oUser = oxNew( "oxuser" );
            $oUser->load( $soxId);

            //show country in active language
            $oCountry = oxNew( "oxCountry" );
            $oCountry->loadInLang( oxLang::getInstance()->getTplLanguage(), $oUser->oxuser__oxcountryid->value );
            $oUser->oxuser__oxcountry = new oxField( $oCountry->oxcountry__oxtitle->value);

            $this->_aViewData["edit"] =  $oUser;
        }

        if (!$this->_allowAdminEdit($soxId))
            $this->_aViewData['readonly'] = true;

        return "user_extend.tpl";
    }

    /**
     * Saves user extended information.
     *
     * @return mixed
     */
    public function save()
    {


        $soxId         = oxConfig::getParameter( "oxid" );
        $aParams       = oxConfig::getParameter( "editval" );
        $blNewsParams  = oxConfig::getParameter( "editnews" );
        $blEmailFailed = oxConfig::getParameter( "emailfailed" );

        if (!$this->_allowAdminEdit($soxId))
            return false;


        $oUser = oxNew( "oxuser" );
        if ( $soxId != "-1")
            $oUser->load( $soxId);
        else
            $aParams['oxuser__oxid'] = null;

        // checkbox handling
        $aParams['oxuser__oxactive'] = $oUser->oxuser__oxactive->value;
        if ( isset( $blNewsParams ) ) {
            $oNewsSubscription = $oUser->getNewsSubscription();
            $oNewsSubscription->setOptInStatus( (int)$blNewsParams );
            $oNewsSubscription->setOptInEmailStatus( (int)$blEmailFailed );
        }

        $oUser->assign( $aParams);
        $oUser->save();

        // set oxid if inserted
        if ( $soxId == "-1")
            oxSession::setVar( "saved_oxid", $oUser->oxuser__oxid->value);

        return $this->autosave();
    }
}
