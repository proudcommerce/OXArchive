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
 * $Id: login.php 17622 2009-03-26 14:02:31Z rimvydas.paskevicius $
 */

/**
 * Administrator login form.
 * Performs administrator login form data collection.
 * @package admin
 */
class Login extends oxAdminView
{
    /**
     * Sets value for _sThisAction to "login".
     */
    public function __construct()
    {
        $this->getConfig()->setConfigParam( 'blAdmin', true );
        $this->_sThisAction  = "login";
    }

    /**
     * Executes parent method parent::render(), creates shop object, sets template parameters
     * and returns name of template file "login.tpl".
     *
     * @return string
     */
    public function render()
    {   $myConfig = $this->getConfig();

        //resets user once on this screen.
        $oUser = oxNew( "oxuser" );
        $oUser->logout();

        oxView::render();

        //if( $myConfig->blDemoMode)
        $oBaseShop = oxNew( "oxshop" );

        $oBaseShop->load( $myConfig->getBaseShopId());
            $sVersion = $oBaseShop->oxshops__oxversion->value;

        $this->getViewConfig()->setViewConfigParam( 'sShopVersion', $sVersion );

        if ( $myConfig->detectVersion() == 1) {   // demo
            $this->addTplParam( "user", "admin");
            $this->addTplParam( "pwd", "admin");
        }
        //#533 user profile
        $this->addTplParam( "profiles", oxUtils::getInstance()->loadAdminProfile($myConfig->getConfigParam( 'aInterfaceProfiles' )));

        // #656 add admin languages
        $aLanguages = array();
        $sSourceDir = $myConfig->getConfigParam('sShopDir') . $myConfig->getTemplateBase( true );

        $iDefLangCache = (int) oxUtilsServer::getInstance()->getOxCookie('oxidadminlanguage');

        // setting template language ..
        $aLangParams = $myConfig->getConfigParam('aLanguageParams');

        $aLangArr = oxLang::getInstance()->getLanguageArray();

        $handle = opendir( $sSourceDir);
        while ( false !== ( $file = readdir( $handle ) ) ) {
            $sLangName = "";
            $iLangNr = 0;

            if ( is_dir("$sSourceDir/$file") && file_exists("$sSourceDir/$file/lang.php") ) {
                    include "$sSourceDir/$file/lang.php";
                    $oLang = new stdClass();
                    $oLang->sValue      = $sLangName;
                    $oLang->blSelected  = ($iLangNr == $iDefLangCache);
                    if ( isset($aLangParams[$file]['baseId']) ) {
                        $iLangNr = $aLangParams[$file]['baseId'];
                    }
                    $aLanguages[$iLangNr] = $oLang;
            }
        }
        $this->addTplParam( "aLanguages", $aLanguages);

        return "login.tpl";
    }

    /**
     * Checks user login data, on success returns "admin_start".
     *
     * @return mixed
     */
    public function checklogin()
    {
        $myUtilsServer = oxUtilsServer::getInstance();

        $user    = oxConfig::getParameter(  'user' );
        $pwd     = oxConfig::getParameter(  'pwd' );
        $profile = oxConfig::getParameter(  'profile' );

        try { // trying to login
            $oUser = oxNew( "oxuser" );
            $oUser->login( $user, $pwd);
        } catch ( oxUserException $oEx ) {
            oxUtilsView::getInstance()->addErrorToDisplay('LOGIN_ERROR');
            $this->addTplParam( 'user', $user );
            $this->addTplParam( 'pwd', $pwd );
            $this->addTplParam( 'profile', $profile );
            return;
        } catch (oxCookieException $oEx) {
            oxUtilsView::getInstance()->addErrorToDisplay('LOGIN_NO_COOKIE_SUPPORT');
            $this->addTplParam( 'user', $user );
            $this->addTplParam( 'pwd', $pwd );
            $this->addTplParam( 'profile', $profile );
            return;
        } catch (oxConnectionException $oEx) {
            oxUtilsView::getInstance()->addErrorToDisplay($oEx);
        }

        // success
        oxUtils::getInstance()->logger( "login successful" );
        // #533
        if ( isset( $profile ) ) {
            $aProfiles = oxSession::getVar( "aAdminProfiles" );
            if ( $aProfiles && isset($aProfiles[$profile])) {
                // setting cookie to store last locally used profile
                $myUtilsServer->setOxCookie ("oxidadminprofile", $profile."@".implode( "@", $aProfiles[$profile]), time()+31536000, "/" );
                oxSession::setVar( "profile", $aProfiles[$profile] );
            }
        } else //deleting cookie info, as setting profile to default
            $myUtilsServer->setOxCookie ("oxidadminprofile", "", time()-3600, "/" );

        // languages
        $iLang = oxConfig::getParameter(  "chlanguage" );
        $myUtilsServer->setOxCookie ("oxidadminlanguage", $iLang, time()+31536000, "/" );
        //P
        //oxSession::setVar( "blAdminTemplateLanguage", $iLang );
        oxLang::getInstance()->setTplLanguage( $iLang );

        return "admin_start";
    }

    /**
     * authorization
     *
     * @return boolean
     */
    protected function _authorize()
    {
        // users are always authorized to use login page
        return true;
    }

    /**
     * Current view ID getter
     *
     * @return string
     */
    public function getViewId()
    {
        return strtolower( get_class( $this ) );
    }
}
