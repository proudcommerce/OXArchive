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
 * @package views
 * @copyright (C) OXID eSales AG 2003-2009
 * @version OXID eShop CE
 * $Id: oxcmp_user.php 23173 2009-10-12 13:29:45Z sarunas $
 */

// defining login/logout states
define( 'USER_LOGIN_SUCCESS', 1 );
define( 'USER_LOGIN_FAIL', 2 );
define( 'USER_LOGOUT', 3 );

/**
 * User object manager.
 * Sets user details data, switches, logouts, logins user etc.
 * @subpackage oxcmp
 */
class oxcmp_user extends oxView
{
    /**
     * Boolean - if user is new or not.
     * @var bool
     */
    protected $_blIsNewUser    = false;

    /**
     * Marking object as component
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Newsletter subscription status
     * @var bool
     */
    protected $_blNewsSubscriptionStatus = null;

    /**
     * User login state marker:
     *  - USER_LOGIN_SUCCESS - user successfully logged in;
     *  - USER_LOGIN_FAIL - login failed;
     *  - USER_LOGOUT - user logged out.
     * @var int
     */
    protected $_iLoginStatus = null;

    /**
     * Sets oxcmp_oxuser::blIsComponent = true, fetches user error
     * code and sets it to default - 0. Executes parent::init().
     *
     * Session variable:
     * <b>usr_err</b>
     *
     * @return null
     */
    public function init()
    {
        // load session user
        $this->_loadSessionUser();

        parent::init();
    }

    /**
     * Executes parent::render(), oxcmp_user::_loadSessionUser(), loads user delivery
     * info. Returns user object oxcmp_user::oUser.
     *
     * Template variables:
     *  <b>invadr</b>, <b>lgn_usr</b>, <b>deladr</b>,
     *
     * Session variables:
     * <b>dgr</b>
     *
     * @return  object  user object
     */
    public function render()
    {
        parent::render();

        // dyn_group feature: if you specify a groupid in URL the user
        // will automatically be added to this group later
        if ( $sDynGoup = oxConfig::getParameter( 'dgr' ) ) {
            oxSession::setVar( 'dgr', $sDynGoup );
        }

        /*
        if ( $blNewsReg = oxConfig::getParameter( 'blnewssubscribed' )) {
            $this->_oParent->setNewsSubscribed( $blNewsReg );
            // Passing to view. Left for compatibility reasons for a while. Will be removed in future
            $this->_oParent->addTplParam( 'blnewssubscribed', $this->_oParent->isNewsSubscribed() );
        }*/

        if ( $aInvAdress = oxConfig::getParameter( 'invadr') ) {
            $this->_oParent->addTplParam( 'invadr', $aInvAdress );
        }

        if ( ( $aDelAdress = oxConfig::getParameter( 'deladr') ) && !oxConfig::getParameter( 'reloadaddress' ) ) {
               $this->_oParent->addTplParam( 'deladr', $aDelAdress );
        }

        if ( $sUser = oxConfig::getParameter( 'lgn_usr' ) ) {
            $this->_oParent->addTplParam( 'lgn_usr', $sUser );
        }

        /*
        if ( $aDelAdressID = oxConfig::getParameter( 'deladrid' ) ) {
            $oAddress = oxNew( 'oxaddress' );
            $oAddress->load( $aDelAdressID );
            $this->_oParent->setDelAddress( $oAddress );
            $this->_oParent->addTplParam( 'delivadr', $this->_oParent->getDelAddress() );
        }

        // clicked on show address ?
        if ( $blShowAddress = oxSession::getVar( 'blshowshipaddress' ) ) {
            $this->_oParent->setShowShipAddress( 1 );
            // Passing to view. Left for compatibility reasons for a while. Will be removed in future
            $this->_oParent->addTplParam( 'blshowshipaddress', 1 );
        }*/

        return $this->getUser();
    }

    /**
     * Tries to load user ID from session.
     *
     * @return null
     */
    protected function _loadSessionUser()
    {
        $myConfig = $this->getConfig();
        $oUser = $this->getUser();

        // no session user
        if ( !$oUser ) {
            return;
        }

        // this user is blocked, deny him
        if ( $oUser->inGroup( 'oxidblocked' ) ) {
            oxUtils::getInstance()->redirect( $myConfig->getShopHomeURL() . 'cl=content&tpl=user_blocked.tpl' );
        }

        // TODO: we need todo something with this !!!
        if ( $oUser->isLoadedFromCookie() ) {

                // #1678 R
                if ( !$myConfig->getConfigParam( 'blPerfNoBasketSaving' ) ) {
                    $myConfig->setGlobalParameter( 'blUserChanged', 1 );
                }

            if ( $oBasket = $this->getSession()->getBasket() ) {
                $oBasket->onUpdate();
            }
        }
    }

    /**
     * Collects posted user information from posted variables ("lgn_usr",
     * "lgn_pwd", "lgn_cook"), executes oxuser::login() and checks if
     * such user exists.
     *
     * Session variables:
     * <b>usr</b>, <b>usr_err</b>
     *
     * Template variables:
     * <b>usr_err</b>
     *
     * @return  string  redirection string
     */
    public function login()
    {
        $sUser     = oxConfig::getParameter( 'lgn_usr' );
        $sPassword = oxConfig::getParameter( 'lgn_pwd' );
        $sCookie   = oxConfig::getParameter( 'lgn_cook' );
        $sOpenId   = oxConfig::getParameter( 'lgn_openid' );

        $this->setLoginStatus( USER_LOGIN_FAIL );

        // trying to login user
        try {
            $oUser = oxNew( 'oxuser' );
            if ( $sOpenId ) {
                $iOldErrorReproting = error_reporting();
                error_reporting($iOldErrorReproting & ~E_STRICT);
                $oOpenId = oxNew( "oxOpenID" );
                $oOpenId->authenticateOid( $sOpenId, $this->_getReturnUrl() );
                error_reporting($iOldErrorReproting);
            } else {
                $oUser->login( $sUser, $sPassword, $sCookie );
            }
            $this->setLoginStatus( USER_LOGIN_SUCCESS );
        } catch ( oxUserException $oEx ) {
            // for login component send excpetion text to a custom component (if defined)
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx, false, true );
            return 'user';
        } catch( oxCookieException $oEx ){
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
            return 'user';
        }
        // finalizing ..
        return $this->_afterLogin( $oUser );
    }

    /**
     * Special functionality which is performed after user logs in (or user is created without pass).
     * Performes additional checking if user is not BLOCKED (oxuser::InGroup("oxidblocked")) - if
     * yes - redirects to blocked user page ("cl=content&tpl=user_blocked.tpl"). If user status
     * is OK - sets user ID to session, automatically assigns him to dynamic
     * group (oxuser::addDynGroup(); if this directive is set (usually
     * by URL)). Stores cookie info if user confirmed in login screen.
     * Then loads delivery info and forces basket to recalculate
     * (oxsession::getBasket() + oBasket::blCalcNeeded = true). Returns
     * "payment" to redirect to payment screen. If problems occured loading
     * user - sets error code according problem, and returns "user" to redirect
     * to user info screen.
     *
     * @param oxuser $oUser user object
     *
     * @return string
     */
    protected function _afterLogin( $oUser )
    {
        $myConfig = $this->getConfig();

        // this user is blocked, deny him
        if ( $oUser->inGroup( 'oxidblocked' ) ) {
            oxUtils::getInstance()->redirect( $myConfig->getShopHomeURL().'cl=content&tpl=user_blocked.tpl' );
        }

        // adding to dyn group
        $oUser->addDynGroup(oxSession::getVar( 'dgr' ), $myConfig->getConfigParam( 'aDeniedDynGroups' ));

        // recalc basket
        if ( $oBasket = $this->getSession()->getBasket() ) {
            $oBasket->onUpdate();
        }

            // #1678 R
            if ( !$myConfig->getConfigParam( 'blPerfNoBasketSaving' ) ) {
                $myConfig->setGlobalParameter( 'blUserChanged', 1);
            }


        return 'payment';
    }

    /**
     * Executes oxcmp_user::login() method. After loggin user will not be
     * redirected to user or payment screens.
     *
     * @return null
     */
    public function login_noredirect()
    {
        $this->login();
    }

    /**
     * Special utility function which is executed right after
     * oxcmp_user::logout is called. Currently it unsets such
     * session parameters as user chosen payment id, delivery
     * address id, active delivery set.
     *
     * @return null
     */
    protected function _afterLogout()
    {
        oxSession::deleteVar( 'paymentid' );
        oxSession::deleteVar( 'sShipSet' );
        oxSession::deleteVar( 'deladrid' );
        oxSession::deleteVar( 'dynvalue' );

        // resetting & recalc basket
        if ( ( $oBasket = $this->getSession()->getBasket() ) ) {
            $oBasket->resetUserInfo();
            $oBasket->onUpdate();
        }
    }

    /**
     * Deletes user information from session:<br>
     * "usr", "dgr", "dynvalue", "paymentid"<br>
     * also deletes cookie, unsets oxconfig::oUser,
     * oxcmp_user::oUser, forces basket to recalculate.
     *
     * @return null
     */
    public function logout()
    {
        $myConfig  = $this->getConfig();
        $oUser = oxNew( 'oxuser' );

        if ( $oUser->logout() ) {

            $this->setLoginStatus( USER_LOGOUT );

            // finalizing ..
            $this->_afterLogout();


            // redirecting if user logs out in SSL mode
            if ( oxConfig::getParameter('redirect') && $myConfig->getConfigParam( 'sSSLShopURL' ) ) {

                oxUtils::getInstance()->redirect( $this->_getLogoutLink());
            }
        }
    }

    /**
     * Executes blUserRegistered = oxcmp_user::_changeUser_noRedirect().
     * if this returns true - returns "payment" (this redirects to
     * payment page), else returns blUserRegistered value.
     *
     * @see oxcmp_user::_changeUser_noRedirect()
     *
     * @return  mixed    redirection string or true if user is registered, false otherwise
     */
    public function changeUser( )
    {
        // checking if "open address area" button was clicked
        // or reloading form when delivery address was selected
        if ( $this->_setupDelAddress() ) {
            return;
        }

        $blUserRegistered = $this->_changeUser_noRedirect( );

        if ( $blUserRegistered === true ) {
            return 'payment';
        } else {
            return $blUserRegistered;
        }
    }

    /**
     * Executes oxcmp_user::_changeuser_noredirect().
     *
     * @return null
     */
    public function changeuser_testvalues()
    {
        // skip updating user info if this is just form reload
        // on selecting delivery address

        $this->_changeUser_noRedirect();
    }

    /**
     * First test if all MUST FILL fields were filled, then performed
     * additional checking oxcmp_user::CheckValues(). If no errors
     * occured - trying to create new user (oxuser::CreateUser()),
     * logging him to shop (oxuser::Login() if user has entered password)
     * or assigning him to dynamic group (oxuser::addDynGroup()).
     * If oxuser::CreateUser() returns false - thsi means user is
     * allready created - we only logging him to shop (oxcmp_user::Login()).
     * If there is any error with missing data - function will return
     * false and set error code (oxcmp_user::iError). If user was
     * created successfully - will return "payment" to redirect to
     * payment interface.
     *
     * Template variables:
     * <b>usr_err</b>
     *
     * Session variables:
     * <b>usr_err</b>, <b>usr</b>
     *
     * @return  mixed    redirection string or true if successful, false otherwise
     */
    public function createUser()
    {
        // checking if "open address area" button was clicked
        if ( $blSetup = $this->_setupDelAddress() ) {
            return;
        }

        $myConfig = $this->getConfig();
        $myUtils  = oxUtils::getInstance();

        // collecting values to check
        $sUser = oxConfig::getParameter( 'lgn_usr' );

        // first pass
        $sPassword = oxConfig::getParameter( 'lgn_pwd' );

        // second pass
        $sPassword2 = oxConfig::getParameter( 'lgn_pwd2' );

        $aRawVal = array('oxuser__oxcompany', 'oxuser__oxaddinfo', 'oxuser__oxfname', 'oxuser__oxlname', 'oxuser__oxcity');
        $aInvAdress = oxConfig::getParameter( 'invadr', $aRawVal );
        $aDelAdress = $this->_getDelAddressData();

        $oUser = oxNew( 'oxuser' );

        try {

            $oUser->checkValues( $sUser, $sPassword, $sPassword2, $aInvAdress, $aDelAdress );

            // setting values
            $oUser->oxuser__oxusername = new oxField($sUser, oxField::T_RAW);
            $oUser->setPassword( $sPassword );
            $oUser->oxuser__oxactive   = new oxField(1, oxField::T_RAW);

            $oUser->createUser();
            $oUser->load( $oUser->getId() );
            $oUser->changeUserData( $oUser->oxuser__oxusername->value, $sPassword, $sPassword, $aInvAdress, $aDelAdress );

            // assigning to newsletter
            $blOptin = oxConfig::getParameter( 'blnewssubscribed' );
            $this->_blNewsSubscriptionStatus = $oUser->setNewsSubscription( $blOptin, true );

            $oUser->logout();

        } catch ( oxUserException $oEx ) {
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx, false, true );
            return false;
        } catch( oxInputException $oEx ){
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx, false, true );
            return false;
        } catch( oxConnectionException $oEx ){
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx, false, true );
            return false;
        }

        if ( !$sPassword ) {
            oxSession::setVar( 'usr', $oUser->getId() );
            $this->_afterLogin( $oUser );
        } elseif ( $this->login() == 'user' ) {
            return false;
        }

        // order remark
        //V #427: order remark for new users
        $sOrd_Remark = oxConfig::getParameter( 'order_remark' );
        if ( $sOrd_Remark ) {
            oxSession::setVar( 'ordrem', $sOrd_Remark );
        }

        // send register eMail
        //TODO: move into user
        if ( (int) oxConfig::getParameter( 'option' ) == 3 ) {
            $oxEMail = oxNew( 'oxemail' );
            $oxEMail->sendRegisterEmail( $oUser );
        }

        // new registered
        $this->_blIsNewUser = true;

        return 'payment';
    }

    /**
     * Creates new oxid user
     *
     * @return string partial parameter string or null
     */
    public function registerUser()
    {
        // checking if "open address area" button was clicked
        if ( $blSetup = $this->_setupDelAddress() ) {
            return;
        }

        // registered new user ?
        if ( $this->createuser()!= false && $this->_blIsNewUser ) {
            // #1672 R
            $this->getUser()->addToGroup( 'oxidnotyetordered' );

            if ( $this->_blNewsSubscriptionStatus === null || $this->_blNewsSubscriptionStatus ) {
                return 'register?success=1';
            } else {
                return 'register?success=1&newslettererror=4';
            }
        } else { // problems with registration ...
            $this->logout();
        }
    }

    /**
     * Mostly used for customer profile editing screen (OXID eShop ->
     * MY ACCOUNT). Checks if oUser is set (oxcmp_user::oUser) - if
     * not - executes oxcmp_user::_loadSessionUser(). If user unchecked newsletter
     * subscription option - removes him from this group. There is an
     * additional MUST FILL fields checking. Function returns true or false
     * according to user data submission status.
     *
     * Session variables:
     * <b>ordrem</b>
     *
     * @return  bool true on success, false otherwise
     */
    protected function _changeUser_noRedirect( )
    {
        // no user ?
        $oUser = $this->getUser();
        if ( !$oUser ) {
            return;
        }

        // collecting values to check
        $aDelAdress = $this->_getDelAddressData();
        // if user company name, user name and additional info has special chars
        $aRawVal = array('oxuser__oxcompany', 'oxuser__oxaddinfo', 'oxuser__oxfname',
                            'oxuser__oxlname', 'oxuser__oxstreet', 'oxuser__oxstreetnr',
                            'oxuser__oxcity', 'oxuser__oxfon', 'oxuser__oxfax',
                            'oxuser__oxmobfon', 'oxuser__oxprivfon');

        $aInvAdress = oxConfig::getParameter( 'invadr', $aRawVal );

        $sUserName  = $oUser->oxuser__oxusername->value;
        $sPassword  = $sPassword2 = $oUser->oxuser__oxpassword->value;

        try { // testing user input
            $oUser->changeUserData( $sUserName, $sPassword, $sPassword2, $aInvAdress, $aDelAdress );
            // assigning to newsletter
            if (($blOptin = oxConfig::getParameter( 'blnewssubscribed' )) === null) {
                $blOptin = $oUser->getNewsSubscription()->getOptInStatus();
            }
            $this->_blNewsSubscriptionStatus = $oUser->setNewsSubscription( $blOptin, $this->getConfig()->getConfigParam( 'blOrderOptInEmail' ) );

        } catch ( oxUserException $oEx ) { // errors in input
            // marking error code
            //TODO
            oxUtilsView::getInstance()->addErrorToDisplay($oEx, false, true);
            return;
        } catch(oxInputException $oEx) {
            oxUtilsView::getInstance()->addErrorToDisplay($oEx, false, true);
            return;
        } catch(oxConnectionException $oEx){
             //connection to external resource broken, change message and pass to the view
            oxUtilsView::getInstance()->addErrorToDisplay($oEx, false, true);
            return;
        }


        // order remark
        $sOrd_Remark = oxConfig::getParameter( 'order_remark' );
        if ( $sOrd_Remark ) {
            oxSession::setVar( 'ordrem', $sOrd_Remark );
        }

        if ( $oBasket = $this->getSession()->getBasket() ) {
            $oBasket->onUpdate();
        }
        return true;
    }

    /**
     * Returns delivery address from request. Before returning array is checked if
     * all needed data is there
     *
     * @return array
     */
    protected function _getDelAddressData()
    {
        // if user company name, user name and additional info has special chars
        $aRawVal = array('oxaddress__oxcompany', 'oxaddress__oxaddinfo', 'oxaddress__oxfname', 'oxaddress__oxlname', 'oxaddress__oxcity');
        $aDelAdress = $aDeladr = oxConfig::getParameter( 'deladr', $aRawVal );

        if ( is_array( $aDeladr ) ) {
            // checking if data is filled
            if ( isset( $aDeladr['oxaddress__oxsal'] ) ) {
                unset( $aDeladr['oxaddress__oxsal'] );
            }
            if ( !count( $aDeladr ) || implode( '', $aDeladr ) == '' ) {
                // resetting to avoid empty records
                $aDelAdress = array();
            }
        }
        return $aDelAdress;
    }

    /**
     * Returns logout link with additional params
     *
     * @return string $sLogoutLink
     */
    protected function _getLogoutLink()
    {
        $myConfig = $this->getConfig();
        $sLogoutLink = $myConfig->getShopSecureHomeUrl();
        if ( $myConfig->isSsl() ) {
            $sLogoutLink = $myConfig->getShopHomeUrl();
        }
        $sLogoutLink .= 'cl='.oxConfig::getParameter('cl').$this->_oParent->getDynUrlParams();
        if ( $sParam = oxConfig::getParameter('anid') ) {
            $sLogoutLink .= '&amp;anid='.$sParam;
        }
        if ( $sParam = oxConfig::getParameter('cnid') ) {
            $sLogoutLink .= '&amp;cnid='.$sParam;
        }
        if ( $sParam = oxConfig::getParameter('mnid') ) {
            $sLogoutLink .= '&amp;mnid='.$sParam;
        }
        if ( $sParam = oxConfig::getParameter('tpl') ) {
            $sLogoutLink .= '&amp;tpl='.$sParam;
        }
        return $sLogoutLink.'&amp;fnc=logout';
    }

    /**
     * Checks if shipping address fields must be displayed and
     * sets into session.
     *
     * Template variables:
     * <b>blshowshipaddress</b>
     *
     * @return null
     */
    protected function _setupDelAddress()
    {
        $blShowIt = false;
        $blShowShipAddress = $blSessShowAddress = (int) oxSession::getVar( 'blshowshipaddress' );

        // user clicked on button to hide
        if ( $blHideAddress = oxConfig::getParameter( 'blhideshipaddress' ) ) {
            $blShowShipAddress = 0;
            $blShowIt = true;

            // unsetting delivery address
            oxSession::deleteVar( 'deladdrid' );
        } else {

            $blShowAddress = oxConfig::getParameter( 'blshowshipaddress' )? 1 : 0;
            // user clicked on button to show
            if ( $blShowAddress != $blSessShowAddress ) {
                $blShowShipAddress = 1;
                $blShowIt = true;
            }
        }

        oxSession::setVar( 'blshowshipaddress', $blShowShipAddress );
        $this->_oParent->_aViewData['blshowshipaddress'] = $blShowShipAddress;

        return $blShowIt;
    }

    /**
     * Collects user information posted from openid server. If user do not exists creates
     * new user and executes oxuser::openIdLogin().
     *
     * @return null
     */
    public function loginOid()
    {
        $this->setLoginStatus( USER_LOGIN_FAIL );

        $iOldErrorReproting = error_reporting();
        //for 3rd part library disabling our E_STRICT error reporting
        error_reporting($iOldErrorReproting & ~E_STRICT);
        try {
            $oOpenId = oxNew( "oxOpenID" );
            $aData = $oOpenId->getOidResponse( $this->_getReturnUrl() );
        } catch ( oxUserException $oEx ) {
            // for login component send excpetion text to a custom component (if defined)
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx, false, true );
        }
        error_reporting($iOldErrorReproting);
        if ( count( $aData ) < 1 ) {
            oxUtils::getInstance()->redirect($this->getConfig()->getShopHomeURL().'cl=register');
        }
        if ( $aData['email'] ) {
            $oUser = oxNew( 'oxuser' );
            $oUser->oxuser__oxusername = new oxField($aData['email'], oxField::T_RAW);

            // if such user does not exist - creating it
            if ( !$oUser->exists() ) {
                $oUser->oxuser__oxpassword = new oxField($oUser->getOpenIdPassword(), oxField::T_RAW);
                $oUser->oxuser__oxactive   = new oxField(1, oxField::T_RAW);
                $oUser->oxuser__oxrights   = new oxField('user', oxField::T_RAW);
                $oUser->oxuser__oxshopid   = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
                list ($sFName, $sLName)    = explode(' ', $aData['fullname']);
                $oUser->oxuser__oxfname    = new oxField($sFName, oxField::T_RAW);
                $oUser->oxuser__oxlname    = new oxField($sLName, oxField::T_RAW);

                $oUser->oxuser__oxsal      = new oxField($this->_getUserTitle($aData['gender']), oxField::T_RAW);
                $oUser->oxuser__oxisopenid = new oxField(1, oxField::T_RAW);
                if ( $sCountryId = $oUser->getUserCountryId( $aData['country'] ) ) {
                    $oUser->oxuser__oxcountryid = new oxField( $sCountryId, oxField::T_RAW );
                }
                if ( $aData['postcode'] ) {
                    $oUser->oxuser__oxzip = new oxField( $aData['postcode'], oxField::T_RAW );
                }
                $oUser->save();
            } else {
                $oUser->load( $oUser->getId() );
                //if existing user loggins first time with openid
                if ( $oUser->oxuser__oxisopenid->value == 0 ) {
                    if ( !$oUser->oxuser__oxpassword->value ) {
                        $oUser->oxuser__oxisopenid = new oxField(1, oxField::T_RAW);
                        $oUser->oxuser__oxpassword = new oxField($oUser->getOpenIdPassword(), oxField::T_RAW);
                    } else {
                        $oUser->oxuser__oxisopenid = new oxField(2, oxField::T_RAW);
                    }
                    $oUser->save();
                }
            }

            try {
                $oUser->openIdLogin( $oUser->oxuser__oxusername->value );
                $this->setLoginStatus( USER_LOGIN_SUCCESS );
            } catch ( oxUserException $oEx ) {
                // for login component send excpetion text to a custom component (if defined)
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx, false, true );
            }

            // finalizing ..
            $this->_afterLogin( $oUser );
            $this->getParent()->setFncName( null );
            oxUtils::getInstance()->redirect($this->getParent()->getLink());
        }
    }

    /**
     * Returns gender for database
     *
     * @param string $sGender F(femail) or M(mail)
     *
     * @return string
     */
    protected function _getUserTitle( $sGender )
    {
        if ( $sGender == "F" ) {
            return oxLang::getInstance()->translateString( "ACCOUNT_USER_MRS" );
        } else {
            return oxLang::getInstance()->translateString( "ACCOUNT_USER_MR" );
        }
    }

    /**
     * Returns return url for openid.
     *
     * @return string $sReturnUrl
     */
    protected function _getReturnUrl()
    {
        $this->getParent()->setFncName( 'loginOid' );
        $sReturnUrl = str_replace( '&amp;', '&', $this->getParent()->getLink() );
        if ( !strpos( $sReturnUrl, 'loginOid' ) ) {
            if ( !strpos( $sReturnUrl, 'loginOid' ) ) {
                $sReturnUrl = $sReturnUrl . "&fnc=loginOid";
            } else {
                $sReturnUrl = $sReturnUrl . "?fnc=loginOid";
            }
        }
        return $sReturnUrl;
    }

    /**
     * Sets user login state
     *
     * @param int $iStatus login state (USER_LOGIN_SUCCESS/USER_LOGIN_FAIL/USER_LOGOUT)
     *
     * @return null
     */
    public function setLoginStatus( $iStatus )
    {
        $this->_iLoginStatus = $iStatus;
    }

    /**
     * Returns user login state marker:
     *  - USER_LOGIN_SUCCESS - user successfully logged in;
     *  - USER_LOGIN_FAIL - login failed;
     *  - USER_LOGOUT - user logged out.
     *
     * @return int
     */
    public function getLoginStatus()
    {
        return $this->_iLoginStatus;
    }
}
