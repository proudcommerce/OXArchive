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
 * @package core
 * @copyright (C) OXID eSales AG 2003-2009
 * @version OXID eShop CE
 * $Id: oxconfig.php 18789 2009-05-05 08:34:18Z arvydas $
 */

define( 'MAX_64BIT_INTEGER', '18446744073709551615' );

/**
 * Main shop configuration class.
 *
 * @package core
 */
class oxConfig extends oxSuperCfg
{
    // this column of params are defined in config.inc.php file,
    // so for backwards compat. names starts without underscore

    /**
     * Database host name
     *
     * @var string
     */
    protected $dbHost = null;

    /**
     * Database name
     *
     * @var string
     */
    protected $dbName = null;

    /**
     * Database user name
     *
     * @var string
     */
    protected $dbUser = null;

    /**
     * Database user password
     *
     * @var string
     */
    protected $dbPwd  = null;

    /**
     * Database driver type
     *
     * @var string
     */
    protected $dbType = null;

    /**
     * Shop Url
     *
     * @var string
     */
    protected $sShopURL = null;

    /**
     * Shop SSL mode Url
     *
     * @var string
     */
    protected $sSSLShopURL = null;

    /**
     * Shops admin SSL mode Url
     *
     * @var string
     */
    protected $sAdminSSLURL = null;

    /**
     * Shops install directory
     *
     * @var string
     */
    protected $sShopDir = null;

    /**
     * Shops compile directory
     *
     * @var string
     */
    protected $sCompileDir = null;

    /**
     * Debug mode (default is 0):
     *  -1 = Logger Messages internal use only
     *   0 = off
     *   1 = smarty
     *   2 = SQL
     *   3 = SQL + smarty
     *   4 = SQL + smarty + shoptemplate data
     *   5 = Delivery Cost calculation info
     *   6 = SMTP Debug Messages
     *   7 = Slow SQL query indication
     *
     * @var int
     */
    protected $iDebug = 0;

    /**
     * Administrator email address, used to send critical notices
     *
     * @var string
     */
    protected $sAdminEmail = null;

    /**
     * Use cookies
     *
     * @var bool
     */
    protected $blSessionUseCookies = null;

    /**
     * Force user to use cookies
     *
     * @var bool
     */
    protected $blSessionEnforceCookies = null;

    /**
     * Default max article count in select lists
     *
     * @var int
     */
    //protected $iMaxArticles = 6000;

    /**
     * Default image loading location.
     * If $blNativeImages is set to true the shop loads images from current domain,
     * otherwise images are loaded from the domain specified in config.inc.php.
     * This is applicable for different domains depending on language or mall
     * if mall mode is available.
     *
     * @var bool
     */
    protected $blNativeImages = true;

    /**
     * Names of tables what are multishop
     *
     * @var array
     */
    protected $aMultiShopTables = array( 'oxarticles', 'oxdiscount', 'oxcategories', 'oxattribute',
                                         'oxlinks', 'oxvoucherseries', 'oxmanufacturers',
                                         'oxnews', 'oxselectlist', 'oxwrapping',
                                         'oxdeliveryset', 'oxdelivery', 'oxvendor', 'oxobject2category');

    /**
     * oxConfig instance
     *
     * @var oxconfig
     */
    private static $_instance = null;

    /**
     * Application starter instance
     *
     * @var oxstart
     */
    private $_oStart = null;


    /**
     * Active shop object.
     *
     * @var object
     */
    protected $_oActShop       = null;

    /**
     * Active View object. Object has setters/getters for these properties:
     *   _sClass - name of current view class
     *   _sFnc   - name of current action function
     *
     * @var object
     */
    protected $_oActView       = null;

    /**
     * Array of global parameters.
     *
     * @var array
     */
    protected $_aGlobalParams = array();

    /**
     * Shop config parameters storage array
     *
     * @var array
     */
    protected $_aConfigParams = array();

    /**
     * Current language Id
     *
     * @var int
     */
    protected $_iLanguageId = null;

    /**
     * Current shop Id
     *
     * @var int
     */
    protected $_iShopId = null;


    /**
     * Out dir name
     *
     * @var string
     */
    protected $_sOutDir = 'out';

    /**
     * Image dir name
     *
     * @var string
     */
    protected $_sImageDir = 'img';

    /**
     * Dyn Image dir name
     *
     * @var string
     */
    protected $_sPictureDir = 'pictures';

    /**
     * Template dir name
     *
     * @var string
     */
    protected $_sTemplateDir = 'tpl';

    /**
     * Resource dir name
     *
     * @var string
     */
    protected $_sResourceDir = 'src';

    /**
     * Whether shop is in SSL mode
     *
     * @var bool
     */
    protected $_blIsSsl = null;

    /**
     * Absolute image dirs for each shops
     *
     * @var array
     */
    protected $_aAbsDynImageDir = array();

    /**
     * Active currency object
     *
     * @var array
     */
    protected $_oActCurrencyObject = null;

    /**
     * Returns config parameter value if such parameter exists
     *
     * @param string $sName config parameter name
     *
     * @return mixed
     */
    public function getConfigParam( $sName )
    {
        if ( isset( $this->$sName ) ) {
            return $this->$sName;
        } elseif ( isset ( $this->_aConfigParams[$sName] ) ) {
            return $this->_aConfigParams[$sName];
        }
    }

    /**
     * Stores config parameter value in cofig
     *
     * @param string $sName  config parameter name
     * @param string $sValue config parameter value
     *
     * @return null
     */
    public function setConfigParam( $sName, $sValue )
    {
        if ( isset( $this->$sName ) ) {
            $this->$sName = $sValue;
        } else {
            $this->_aConfigParams[$sName] = $sValue;
        }
    }

    /**
     * Starts session manager
     *
     * @return null
     */
    public function init()
    {
        include getShopBasePath().'config.inc.php';
        include getShopBasePath().'core/oxconfk.php';

        //adding trailing slashes
        $oFileUtils = oxUtilsFile::getInstance();
        $this->sShopDir     = $oFileUtils->normalizeDir($this->sShopDir);
        $this->sCompileDir  = $oFileUtils->normalizeDir($this->sCompileDir);
        $this->sShopURL     = $oFileUtils->normalizeDir($this->sShopURL);
        $this->sSSLShopURL  = $oFileUtils->normalizeDir($this->sSSLShopURL);
        $this->sAdminSSLURL = $oFileUtils->normalizeDir($this->sAdminSSLURL);


        // some important defaults
        if( !$this->getConfigParam( 'sDefaultLang' ) )
            $this->setConfigParam( 'sDefaultLang', 0 );

        $blLogChangesInAdmin = $this->getConfigParam( 'blLogChangesInAdmin' );
        if( !isset( $blLogChangesInAdmin ) )
            $this->setConfigParam( 'blLogChangesInAdmin', false );

        $blCheckTemplates = $this->getConfigParam( 'blCheckTemplates' );
        if( !isset( $blCheckTemplates ) )
            $this->setConfigParam( 'blCheckTemplates', false );

        $blAllowArticlesubclass = $this->getConfigParam( 'blAllowArticlesubclass' );
        if( !isset( $blAllowArticlesubclass ) )
            $this->setConfigParam( 'blAllowArticlesubclass', false );

        $iAdminListSize = $this->getConfigParam( 'iAdminListSize' );
        if( !isset( $iAdminListSize ) )
            $this->setConfigParam( 'iAdminListSize', 9 );

        // #1173M  for EE - not all pic are deleted
        $iPicCount = $this->getConfigParam( 'iPicCount' );
        if( !isset( $iPicCount ) )
            $this->setConfigParam( 'iPicCount', 12 );

        $iZoomPicCount = $this->getConfigParam( 'iZoomPicCount' );
        if( !isset( $iZoomPicCount ) )
            $this->setConfigParam( 'iZoomPicCount', 4 );

        //max shop id default value
        $iMaxShopId = $this->getConfigParam( 'iMaxShopId' );
        if( !isset( $iMaxShopId ) )
            $this->setConfigParam( 'iMaxShopId', 64 );

        // disabling caching according to DODGER #655 : disable Caching as it doesnt work good enought
        $this->setConfigParam( 'blTemplateCaching', false );

        //setting ADODB timeout
        global  $ADODB_SESS_LIFE;
        $ADODB_SESS_LIFE  = 1;


            /*
        $iSessionTimeout = null;
        if ( $this->isAdmin())
            $iSessionTimeout = $this->getConfigParam( 'iSessionTimeoutAdmin' );
        if ( !$this->isAdmin() || !$iSessionTimeout )
            $iSessionTimeout = $this->getConfigParam( 'iSessionTimeout' );
        if (!$iSessionTimeout)
            $iSessionTimeout = 60;*/

        // ADODB cachelifetime
        $iDBCacheLifeTime = $this->getConfigParam( 'iDBCacheLifeTime' );
        if( !isset( $iDBCacheLifeTime ) )
            $this->setConfigParam( 'iDBCacheLifeTime', 3600 ); // 1 hour

        $sCoreDir = $this->getConfigParam( 'sShopDir' );
        $this->setConfigParam( 'sCoreDir', $sCoreDir.'/core/' );

        try {
            //starting up the session
            $this->getSession()->start();

            $sShopID = $this->getShopId();

            // load now
            $this->_loadVarsFromDb( $sShopID );

        } catch ( oxConnectionException $oEx ) {
            $oEx->debugOut( $this->iDebug);
            if ( defined( 'OXID_PHP_UNIT' ) ) {
                return false;
            } elseif ( 0 != $this->iDebug ) {
                exit( $oEx->getString() );
            } else {
                header( "HTTP/1.1 500 Internal Server Error");
                header( "Location: offline.html");
                header( "Connection: close");
            }
        } catch ( oxCookieException $oEx ) {
            // redirect to start page and display the error
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
            oxUtils::getInstance()->redirect( $this->getShopHomeURL() .'cl=start' );
        }

        //application initialization
        $this->_oStart = new oxStart();
        $this->_oStart->appInit();
    }

    /**
     * Returns singleton oxConfig object instance or create new if needed
     *
     * @return oxConfig
     */
    public static function getInstance()
    {

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modConfig::$unitMOD ) && is_object( modConfig::$unitMOD ) ) {
                return modConfig::$unitMOD;
            }
        }

        if ( !self::$_instance instanceof oxConfig ) {
                //exceptions from here go directly to global exception handler
                //if no init is possible whole application has to die!
                self::$_instance = new oxConfig();
                self::$_instance->init();
        }
        return self::$_instance;
    }

    /**
     * Load config values from DB
     *
     * @param string $sShopID   shop ID to load parameters
     * @param array  $aOnlyVars array of params to load (optional)
     *
     * @return null
     */
    protected function _loadVarsFromDb( $sShopID, $aOnlyVars = null )
    {

        $sQ = "select oxvarname, oxvartype, DECODE( oxvarvalue, '".$this->getConfigParam( 'sConfigKey' )."') as oxvarvalue from oxconfig where oxshopid = '$sShopID'";
        // dodger, allow loading from some vars only from baseshop
        if ( $aOnlyVars !== null ) {
            $blSep = false;
            $sIn  = '';
            foreach ( $aOnlyVars as $sField ) {
                if ( $blSep ) {
                    $sIn .= ', ';
                }
                $sIn .= '"'.$sField.'"';
                $blSep = true;
            }
            $sQ .= ' and oxvarname in ( '.$sIn.' ) ';
        }

        $oRs = oxDb::getDb()->execute( $sQ );
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sVarName = $oRs->fields[0];
                $sVarType = $oRs->fields[1];
                $sVarVal  = $oRs->fields[2];

                //in sShopURL and sSSLShopURL cases we skip (for admin or when URL values are not set)
                if ( ( $sVarName == 'sShopURL' || $sVarName == 'sSSLShopURL' ) &&
                    ( !$sVarVal || $this->isAdmin() === true ) ) {
                    $oRs->moveNext();
                    continue;
                }

                switch ( $sVarType ) {
                    case 'arr':
                    case 'aarr':
                        $this->setConfigParam( $sVarName, unserialize( $sVarVal ) );
                        break;
                    case 'bool':
                        $this->setConfigParam( $sVarName, ( $sVarVal == 'true' || $sVarVal == '1' ) );
                        break;
                    default:
                        $this->setConfigParam( $sVarName, $sVarVal );
                }


                if ( $sVarType == 'arr' || $sVarType == 'aarr' ) {
                } elseif ( $sVarType == 'bool' ) {
                } else {

                }

                $oRs->moveNext();
            }
        }

    }

    /**
     * Unsets all session data.
     *
     * @return null
     */
    public function pageClose()
    {
        return $this->_oStart->pageClose();
    }

    /**
     * Returns value of parameter stored in POST,GET.
     * This method returns parameter stored in session as well, but this functionality is deprecated
     * and will be removed in future
     * For security reasons performed oxconfig::checkSpecialChars().
     * use $blRaw very carefully if you want to get unescaped
     * parameter.
     *
     * @param string $sName Name of parameter
     * @param bool   $blRaw Get unescaped parameter
     *
     * @return mixed
     */
    public static function getParameter(  $sName, $blRaw = false )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modConfig::$unitMOD ) && is_object( modConfig::$unitMOD ) ) {
                try{
                    return modConfig::getParameter(  $sName, $blRaw );
                } catch( Exception $e ) {
                    // if exception is thrown, use default
                }
            }
        }

        $sValue = null;
        if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST[$sName] ) ) {
            $sValue = $_POST[$sName];
        } elseif ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'GET' && isset( $_GET[$sName] ) ) {
            $sValue = $_GET[$sName];
            //<deprecated>
        } elseif ( oxSession::hasVar( $sName ) ) {
            $sValue = oxSession::getVar( $sName );
            //</deprecated>
        } else {
            $sValue = null;
        }

        // TODO: remove this after special charts concept implementation
        $blIsAdmin = oxConfig::getInstance()->isAdmin() && oxSession::getVar("blIsAdmin");
        if ( $sValue != null && !$blIsAdmin && (!$blRaw || is_array($blRaw))) {
            self::checkSpecialChars( $sValue, $blRaw );
        }

        return $sValue;
    }

    /**
     * Returns uploaded file parameter
     *
     * @param array $sParamName param name
     *
     * @return null
     */
    public function getUploadedFile($sParamName)
    {
        return $_FILES[$sParamName];
    }

    /**
     * Sets global parameter value
     *
     * @param string $sName  name of parameter
     * @param mixed  $sValue value to store
     *
     * @return null
     */
    public function setGlobalParameter( $sName, $sValue )
    {
        $this->_aGlobalParams[$sName] = $sValue;
    }

    /**
     * Returns global parameter value
     *
     * @param string $sName name of cached parameter
     *
     * @return mixed
     */
    public function getGlobalParameter( $sName )
    {
        if ( isset( $this->_aGlobalParams[$sName] ) ) {
            return $this->_aGlobalParams[$sName];
        } else {
            return null;
        }
    }

    /**
     * Checks if passed parameter has special chars and replaces them.
     * Returns checked value.
     *
     * @param mixed &$sValue value to process escaping
     * @param array $aRaw    keys of unescaped values
     *
     * @return mixed
     */
    public static function checkSpecialChars( & $sValue, $aRaw = null )
    {
        if ( is_object( $sValue ) ) {
            return $sValue;
        }

        if ( is_array( $sValue ) ) {
            $newValue = array();
            foreach ( $sValue as $sKey => $sVal ) {
                $sValidKey = $sKey;
                if ( !$aRaw || !in_array($sKey, $aRaw) ) {
                    self::checkSpecialChars( $sValidKey );
                    self::checkSpecialChars( $sVal );
                    if ($sValidKey != $sKey) {
                        unset ($sValue[$sKey]);
                    }
                }
                $newValue[$sValidKey] = $sVal;
            }
            $sValue = $newValue;
        } else {
            $sValue = str_replace( array( '&',     '<',    '>',    '"',      "'",      chr(0), '\\' ),
                                   array( '&amp;', '&lt;', '&gt;', '&quot;', '&#039;', '',     '&#092;' ),
                                   $sValue );
        }
        return $sValue;
    }

    /**
     * Returns active shop ID.
     *
     * @return int
     */
    public function getShopId()
    {
        if ( $this->_iShopId !== null )
            return $this->_iShopId;

            $this->_iShopId = $this->getBaseShopId();


        oxSession::setVar( 'actshop', $this->_iShopId );
        return $this->_iShopId;
    }

    /**
     * Active Shop id setter
     *
     * @param string $sShopId shop id
     *
     * @return null
     */
    public function setShopId( $sShopId )
    {
        oxSession::setVar( 'actshop', $sShopId );
        $this->_iShopId = $sShopId;
    }


    /**
     * Checks if WEB session is SSL. Returns true if yes.
     *
     * @return bool
     */
    public function isSsl()
    {
        if ( is_null( $this->_blIsSsl ) ) {

            $myUtilsServer   = oxUtilsServer::getInstance();
            $aServerVars     = $myUtilsServer->getServerVar();
            $aHttpsServerVar = $myUtilsServer->getServerVar( 'HTTPS' );

            $this->_blIsSsl = ( isset( $aHttpsServerVar ) && $this->getConfigParam( 'sSSLShopURL' ) &&
                         ( $aHttpsServerVar == 'on' || $aHttpsServerVar == '1' ) ); // 1&1 provides "1"

            //additional special handling for profihost customers
            if ( isset( $aServerVars['HTTP_X_FORWARDED_SERVER'] ) &&
                 ( strpos( $aServerVars['HTTP_X_FORWARDED_SERVER'], 'ssl' ) !== false ||
                 strpos( $aServerVars['HTTP_X_FORWARDED_SERVER'], 'secure-online-shopping.de' ) !== false ) ) {
                $this->_blIsSsl = true;
            }
        }

        return $this->_blIsSsl;
    }

    /**
     * Compares current URL to supplied string
     *
     * @param string $sURL URL
     *
     * @return bool true if $sURL is equal to current page URL
     */
    public function isCurrentUrl( $sURL )
    {
        if ( !$sURL ) {
            return false;
        }

        $sCurrentHost = preg_replace( '/\/\w*\.php.*/', '', $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'] );

        //remove double slashes all the way
        $sCurrentHost = str_replace( '/', '', $sCurrentHost );
        $sURL = str_replace( '/', '', $sURL );

        //so far comparing for the host is enought for us
        if ( getStr()->strpos( $sURL, $sCurrentHost ) !== false ) {
            return true;
        }

        return false;
    }

    /**
     * Returns config sShopURL or sMallShopURL if secondary shop
     *
     * @param int  $iLang   language
     * @param bool $blAdmin if admin
     *
     * @return string
     */
    public function getShopUrl( $iLang = null, $blAdmin = null )
    {
        $blAdmin = isset( $blAdmin ) ? $blAdmin : $this->isAdmin();
        if ( $blAdmin ) {
            return $this->getConfigParam( 'sShopURL' );
        }

        // #680 per language another URL
        $iLang = isset( $iLang ) ? $iLang : oxLang::getInstance()->getBaseLanguage();
        $aLanguageURLs = $this->getConfigParam( 'aLanguageURLs' );
        if ( isset( $iLang ) && isset( $aLanguageURLs[$iLang] ) && !empty( $aLanguageURLs[$iLang] ) ) {
            $aLanguageURLs[$iLang] = oxUtils::getInstance()->checkUrlEndingSlash( $aLanguageURLs[$iLang] );
            return $aLanguageURLs[$iLang];
        }

        //normal section
        $sMallShopURL = $this->getConfigParam( 'sMallShopURL' );
        if ( $sMallShopURL ) {
            $sMallShopURL = oxUtils::getInstance()->checkUrlEndingSlash( $sMallShopURL );
            return $sMallShopURL;
        }

        return $this->getConfigParam( 'sShopURL' );
    }

    /**
     * Returns config sSSLShopURL or sMallSSLShopURL if secondary shop
     *
     * @param int $iLang language (default is null)
     *
     * @return string
     */
    public function getSslShopUrl( $iLang = null )
    {
        // #680 per language another URL
        $iLang = isset( $iLang ) ? $iLang : oxLang::getInstance()->getBaseLanguage();
        $aLanguageSSLURLs = $this->getConfigParam( 'aLanguageSSLURLs' );
        if ( isset( $iLang ) && isset( $aLanguageSSLURLs[$iLang] ) && !empty( $aLanguageSSLURLs[$iLang] ) ) {
            $aLanguageSSLURLs[$iLang] = oxUtils::getInstance()->checkUrlEndingSlash( $aLanguageSSLURLs[$iLang] );
            return $aLanguageSSLURLs[$iLang];
        }

        //mall mode
        if ( ( $sMallSSLShopURL = $this->getConfigParam( 'sMallSSLShopURL' ) ) ) {
            $sMallSSLShopURL = oxUtils::getInstance()->checkUrlEndingSlash( $sMallSSLShopURL );
            return $sMallSSLShopURL;
        }

        if ( ( $sMallShopURL = $this->getConfigParam( 'sMallShopURL' ) ) ) {
            $sMallShopURL = oxUtils::getInstance()->checkUrlEndingSlash( $sMallShopURL );
            return $sMallShopURL;
        }

        //normal section
        if ( ( $sSSLShopURL = $this->getConfigParam( 'sSSLShopURL' ) ) ) {
            return $sSSLShopURL;
        }

        return $this->getShopUrl( $iLang );
    }

    /**
     * Returns utils dir URL
     *
     * @return string
     */
    public function getCoreUtilsUrl()
    {
        if ( ( $sSSLShopURL = $this->getConfigParam( 'sSSLShopURL' ) ) ) {
            return $sSSLShopURL.'core/utils/';
        }
        return $this->getConfigParam( 'sShopURL' ).'core/utils/';
    }

    /**
     * Returns SSL or non SSL shop URL without index.php depending on Mall
     *
     * @return string
     */
    public function getCurrentShopUrl()
    {
        if ( $this->isSsl() ) {
            return $this->getSslShopUrl();
        }
        return $this->getShopUrl();
    }

    /**
     * Returns SSL or not SSL shop URL with index.php and sid
     *
     * @param int $iLang language (optional)
     *
     * @return string
     */
    public function getShopCurrentUrl( $iLang = null )
    {
        if ( $this->isSsl() ) {
            $sURL = $this->getSSLShopURL( $iLang );
        } else {
            $sURL = $this->getShopURL( $iLang );
        }

        return $this->getSession()->url( $sURL.'index.php' );
    }

    /**
     * Returns shop non SSL URL including index.php and sid.
     *
     * @param int  $iLang   language
     * @param bool $blAdmin if admin
     *
     * @return string
     */
    public function getShopHomeUrl( $iLang = null, $blAdmin = null )
    {
        return $this->getSession()->url( $this->getShopUrl( $iLang, $blAdmin).'index.php' );
    }

    /**
     * Returns shop SSL URL with index.php and sid.
     *
     * @return string
     */
    public function getShopSecureHomeUrl()
    {
        return $this->getSession()->url( $this->getSslShopUrl().'index.php' );
    }

    /**
     * Returns active shop currency.
     *
     * @return string
     */
    public function getShopCurrency()
    {
        return (int) oxConfig::getParameter( 'currency' );
    }

    /**
     * Returns active shop currency object.
     *
     * @return object
     */
    public function getActShopCurrencyObject()
    {
        //caching currency as it does not change through the script
        //but not for unit tests as ther it changes always
        if ( !defined( 'OXID_PHP_UNIT' ) ) {
            if (!is_null($this->_oActCurrencyObject)) {
                return $this->_oActCurrencyObject;
            }
        }

        $iCur = oxConfig::getParameter( 'cur' );
        if ( !isset( $iCur ) ) {
            $iCur = $this->getShopCurrency();
        }

        $aCurrencies = $this->getCurrencyArray();
        if ( !isset( $aCurrencies[$iCur] ) ) {
            return $this->_oActCurrencyObject = reset( $aCurrencies ); // reset() returns the first element
        }

        return $this->_oActCurrencyObject = $aCurrencies[$iCur];
    }

    /**
     * Sets the actual currency
     *
     * @param int $iCur 0 = EUR, 1 = GBP, 2 = CHF
     *
     * @return null
     */
    public function setActShopCurrency( $iCur )
    {
        $aCurrencies = $this->getCurrencyArray();
        if ( isset( $aCurrencies[$iCur] ) ) {
            oxSession::setVar( 'currency', $iCur );
            $this->_oActCurrencyObject = null;
        }
    }


    /**
     * Returns image dir URL (no SSL).
     *
     * @param bool $blAdmin mode - admin/non-admin (default is false)
     *
     * @deprecated use getImageUrl
     *
     * @return string
     */
    public function getNoSslImageDir( $blAdmin = false )
    {
        return $this->getImageUrl( $blAdmin, false );
    }

    /**
     * Returns absolute path to admin images.
     *
     * @deprecated use getImageDir
     *
     * @return string
     */
    public function getAbsAdminImageDir()
    {
        return $this->getImageDir( true );

    }

    /**
     * Formats output directory depending on given parameters. Resources could be language dependant or multilanguage, to be located in theme dir or in default oxbaseshop, 1 or admin dirs. formatOutDir tries to locate resource $sFile in one of these dirs and return that dir.
     * Example result could be /out/basic/1/de/lang.txt when you are looking for "/de/lang.txt" resource ($sFile).
     *
     * @param string $sFile      Resource name - image, template file or dir name (generally from out dir).
     * @param int    $iLang      Custom language
     * @param bool   $blAdmin    Whether to force admin dirs
     * @param bool   $blNoThemes Whether to force the theme dir.
     * @param string $iShop
     *
     * @deprecated use getDir
     *
     * @return string
     */
    /* is not used
    public function formatOutDir($sFile, $iLang = null, $blAdmin = false, $blNoThemes = false, $iShop = null)
    {
        //resolving theme and shop dir
        $sThemeDir  = ($blNoThemes || $blAdmin) ? "" : $this->getConfigParam( 'sTheme' );

        if (is_null($iShop))
            $iShop = $this->getShopId();

        $aShopDirs = array();


        if ($sThemeDir)
            $aShopDirs[] = "$sThemeDir/1/";
        else
            $aShopDirs[] = $this->getBaseShopId() . "/";

        //admin dir
        if ( $blAdmin ) {
            $aShopDirs = array('admin/');
        }

        if ($sFile) {
            //language dir
            if (is_null($iLang))
                $iLang = oxLang::getInstance()->getBaseLanguage();

            $aLangs    = oxLang::getInstance()->getLanguageArray();
            //specific language dir and top dir
            $aLangDirs = array();
            if (isset($aLangs[$iLang]))
                $aLangDirs[] = strtolower($aLangs[$iLang]->abbr . "/");
            $aLangDirs[] = "";


            //finally checking dir
            foreach ($aShopDirs as $sShopDir) {
                foreach ($aLangDirs as $sLangDir) {
                    $sOut = "out/" . $sShopDir . $sLangDir . $sFile;
                    if (file_exists($this->getConfigParam('sShopDir'). $sOut))
                        return $sOut;
                }
            }
        }

        //not language specific default dir
        foreach ($aShopDirs as $sShopDir) {
            $sOut = "out/" . $sShopDir;
            if (file_exists($this->getConfigParam('sShopDir'). $sOut))
                return $sOut;
        }
    }*/

    /**
     * Returns path to out dir
     *
     * @param bool $blAbsolute mode - absolute/relative path
     *
     * @return string
     */
    public function getOutDir( $blAbsolute = true)
    {
        if ($blAbsolute) {
            return $this->getConfigParam('sShopDir').$this->_sOutDir.'/';
        } else {
            return $this->_sOutDir.'/';
        }
    }

    /**
     * Returns url to out dir
     *
     * @param bool $blSSL       Whether to force ssl
     * @param bool $blNativeImg Whether to force native image dirs
     * @param bool $blAdmin     Whether to force admin
     *
     * @return string
     */
    public function getOutUrl( $blSSL = null , $blAdmin = null, $blNativeImg = false )
    {
        $blSSL    = is_null($blSSL)?$this->isSsl():$blSSL;
        $blAdmin  = is_null($blAdmin)?$this->isAdmin():$blAdmin;

        if ( $blSSL ) {
            $sUrl = ($blNativeImg && !$blAdmin )?$this->getSslShopUrl():$this->getConfigParam( 'sSSLShopURL');
        } else {
            $sUrl = ($blNativeImg && !$blAdmin )?$this->getShopUrl():$this->getConfigParam( 'sShopURL' );
        }

        return $sUrl.$this->_sOutDir.'/';
    }

    /**
     * Finds and returns files or folders path in out dir
     *
     * @param string $sFile      File name
     * @param string $sDir       Directory name
     * @param bool   $blAdmin    Whether to force admin
     * @param int    $iLang      Language id
     * @param int    $iShop      Shop id
     * @param string $sTheme     Theme name
     * @param bool   $blAbsolute mode - absolute/relative path
     *
     * @return string
     */
    public function getDir($sFile, $sDir, $blAdmin, $iLang = null, $iShop = null, $sTheme = null, $blAbsolute = true )
    {
        $sBase    = $this->getOutDir( $blAbsolute );
        $sAbsBase = $this->getOutDir();

        $oLang = oxLang::getInstance(); //getTplLanguage

        if ( is_null($iLang) ) {
            $iLang = $oLang->getEditLanguage();
        }

        $sLang = $oLang->getLanguageAbbr( $iLang );

        if ( is_null($iShop) ) {
            $iShop = $this->getShopId();
        }

        if ( is_null($sTheme) ) {
            $sTheme = $this->getConfigParam( 'sTheme' );
        }

        if ( $blAdmin ) {
            $sTheme = 'admin';
        }

        //Load from
        $sPath = "$sTheme/$iShop/$sLang/$sDir/$sFile";
        $sCacheKey = $sPath . "_$blAbsolute";

        if ( ( $sReturn = oxutils::getInstance()->fromStaticCache( $sCacheKey ) ) !== null ) {
            return $sReturn;
        }

        $sReturn = false;

        //test lang level ..
        if ( !$sReturn && !$blAdmin && ( is_readable( $sAbsBase.$sPath ) || is_dir( realpath( $sAbsBase.$sPath ) ) ) ) {
            $sReturn = $sBase . $sPath;
        }

        //test shop level ..
        $sPath = "$sTheme/$iShop/$sDir/$sFile";
        if ( !$sReturn && !$blAdmin && ( is_readable( $sAbsBase.$sPath ) || is_dir( realpath( $sAbsBase.$sPath ) ) ) ) {
            $sReturn = $sBase . $sPath;
        }


        //test theme language level ..
        $sPath = "$sTheme/$sLang/$sDir/$sFile";
        if ( !$sReturn && ( is_readable( $sAbsBase.$sPath ) || is_dir( realpath( $sAbsBase.$sPath )) ) ) {
            $sReturn = $sBase . $sPath;
        }

        //test theme level ..
        $sPath = "$sTheme/$sDir/$sFile";
        if ( !$sReturn && ( is_readable( $sAbsBase.$sPath ) || is_dir( realpath( $sAbsBase.$sPath )) ) ) {
            $sReturn = $sBase . $sPath;
        }

        //test out language level ..
        $sPath = "$sLang/$sDir/$sFile";
        if ( !$sReturn && ( is_readable( $sAbsBase.$sPath ) || is_dir( realpath( $sAbsBase.$sPath )) ) ) {
            $sReturn = $sBase . $sPath;
        }

        //test out level ..
        $sPath = "$sDir/$sFile";
        if ( !$sReturn && ( is_readable( $sAbsBase.$sPath ) || is_dir( realpath( $sAbsBase.$sPath )) ) ) {
            $sReturn = $sBase . $sPath;
        }

        if ( !$sReturn ) {
            // TODO: log missing paths...
        }

        // to cache
        oxutils::getInstance()->toStaticCache( $sCacheKey, $sReturn );

        return $sReturn;
    }

    /**
     * Finds and returns file or folder url in out dir
     *
     * @param string $sFile       File name
     * @param string $sDir        Directory name
     * @param bool   $blAdmin     Whether to force admin
     * @param bool   $blSSL       Whether to force ssl
     * @param bool   $blNativeImg Whether to force native image dirs
     * @param int    $iLang       Language id
     * @param int    $iShop       Shop id
     * @param string $sTheme      Theme name
     *
     * @return string
     */
    public function getUrl($sFile, $sDir , $blAdmin = null, $blSSL = null, $blNativeImg = false, $iLang = null , $iShop = null , $sTheme = null )
    {
        $sUrl = str_replace(
                                $this->getOutDir(),
                                $this->getOutUrl($blSSL, $blAdmin, $blNativeImg),
                                $this->getDir( $sFile, $sDir, $blAdmin, $iLang, $iShop, $sTheme )
                            );
        return $sUrl;
    }

    /**
     * Finds and returns image files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getImagePath( $sFile, $blAdmin = false )
    {
        return $this->getDir( $sFile, $this->_sImageDir, $blAdmin );
    }

    /**
     * Finds and returns image folder url
     *
     * @param bool $blAdmin     Whether to force admin
     * @param bool $blSSL       Whether to force ssl
     * @param bool $blNativeImg Whether to force native image dirs
     *
     * @return string
     */
    public function getImageUrl( $blAdmin = false, $blSSL = null, $blNativeImg = null )
    {
        $blNativeImg = is_null($blNativeImg)?$this->getConfigParam( 'blNativeImages' ):$blNativeImg;
        return $this->getUrl( null, $this->_sImageDir, $blAdmin, $blSSL, $blNativeImg );
    }

    /**
     * Finds and returns image folders path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getImageDir( $blAdmin = false )
    {
        return $this->getDir( null, $this->_sImageDir, $blAdmin );
    }

    /**
     * Finds and returns product pictures files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getPicturePath($sFile, $blAdmin = false )
    {
        return $this->getDir( $sFile, $this->_sPictureDir, $blAdmin );
    }

    /**
     * Finds and returns product picture file or folder url
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param bool   $blSSL   Whether to force ssl
     * @param int    $iLang   Language
     * @param int    $iShopId Shop id
     *
     * @return string
     */
    public function getPictureUrl( $sFile, $blAdmin = false , $blSSL = null , $iLang = null, $iShopId = null )
    {
        if ( $sAltUrl = $this->getConfigParam( 'sAltImageDir' ) ) {

            if ( $this->isSsl() ) {
                $sAltUrl = str_replace( 'http://', 'https://', $sAltUrl );
            }

            if ( !is_null( $sFile ) ) {
                $sAltUrl .= $sFile;
            }

            return $sAltUrl;
        }
        $sUrl = $this->getUrl( $sFile, $this->_sPictureDir, $blAdmin, $blSSL, null, $iLang, $iShopId );
        if ( $sFile && $this->getConfigParam('blFormerTplSupport') ) {
            $sUrl = str_replace( $this->getPictureUrl( null, $blAdmin, $blSSL, $iLang, $iShopId ), '', $sUrl );
        }
        //anything is better than empty name, because <img src=""> calls shop once more = x2 SLOW.
        if (!$sUrl) {
            return $this->getTemplateUrl()."../".$this->_sPictureDir."/0/nopic.jpg";
        }
        return $sUrl;
    }

    /**
     * Finds and returns product pictures folders path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getPictureDir( $blAdmin )
    {
        return $this->getDir( null, $this->_sPictureDir, $blAdmin );
    }

    /**
     * Finds and returns templates files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getTemplatePath( $sFile, $blAdmin )
    {
        return $this->getDir( $sFile, $this->_sTemplateDir, $blAdmin );
    }

    /**
     * Finds and returns templates folders path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getTemplateDir( $blAdmin = false )
    {
        return $this->getDir( null, $this->_sTemplateDir, $blAdmin );
    }

    /**
     * Finds and returns template file or folder url
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param bool   $blSSL   Whether to force ssl
     * @param int    $iLang   Language id
     *
     * @return string
     */
    public function getTemplateUrl( $sFile = null, $blAdmin = false, $blSSL = null , $iLang = null )
    {
        return $this->getUrl( $sFile, $this->_sTemplateDir, $blAdmin, $blSSL, false, $iLang );
    }

    /**
     * Finds and returns base template folder url
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getTemplateBase( $blAdmin = false )
    {
        // Base template dir is the parent dir of template dir
        return str_replace( $this->_sTemplateDir.'/', '', $this->getDir( null, $this->_sTemplateDir, $blAdmin, null, null, null, false ));
    }

    /**
     * Finds and returns resouce (css, js, etc..) files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getResourcePath($sFile, $blAdmin = false )
    {
        return $this->getDir( $sFile, $this->_sResourceDir, $blAdmin );
    }

    /**
     * Finds and returns resouce (css, js, etc..) file or folder url
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param bool   $blSSL   Whether to force ssl
     * @param int    $iLang   Language id
     *
     * @return string
     */
    public function getResourceUrl( $sFile, $blAdmin = false , $blSSL = null , $iLang = null )
    {
        return $this->getUrl( $sFile, $this->_sResourceDir, $blAdmin, $blSSL, false, $iLang );
    }

    /**
     * Finds and returns resouce (css, js, etc..) folders path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getResourceDir( $blAdmin )
    {
        return $this->getDir( null, $this->_sResourceDir, $blAdmin );
    }

    /**
     * Finds and returns language files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param int    $iLang   Language id
     *
     * @return string
     */
    public function getLanguagePath( $sFile, $blAdmin, $iLang = null )
    {
        return $this->getDir( $sFile, oxLang::getInstance()->getLanguageAbbr( $iLang ), $blAdmin, $iLang );
    }

    /**
     * Finds and returns language folders path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getLanguageDir( $blAdmin )
    {
        return $this->getDir( null, null, $blAdmin );
    }

    /**
     * Returns absolute path to images.
     *
     * @deprecated use getImageDir
     *
     * @return string
     */
    public function getAbsImageDir()
    {
        return $this->getImageDir();
    }

    /**
     * Returns url to Dyn images.
     *
     * @param string $sOverrideShopId Shop ID (default null)
     * @param bool   $blNoSsl         SSL status (default null)
     *
     * @deprecated
     *
     * @return string
     */
    public function getDynImageDir( $sOverrideShopId = null, $blNoSsl = null )
    {
        return $this->getPictureUrl(null, false, $this->isSsl() && !$blNoSsl, null, $sOverrideShopId);

        /*

        $sCacheKey = "getDynImageDir_" . $this->isSsl() . "_" . $this->getShopId() . "_" .oxLang::getInstance()->getTplLanguage() . "_" . $sOverrideShopId . "_" . $blNoSsl;
        $sImageDir = oxUtils::getInstance()->fromStaticCache($sCacheKey);

        if ($sImageDir)
            return $sImageDir;

        if ( $sAltImageDir = $this->getConfigParam( 'sAltImageDir' ) ) {
            if ( $this->isSsl() ) {
                $sAltImageDir = str_replace( 'http://', 'https://', $sAltImageDir );
            }
            oxUtils::getInstance()->toStaticCache($sCacheKey, $sAltImageDir);
            return $sAltImageDir;
        }

        //Tomas 2005-01-19
        //now it loads images only from either active shop either from oxbaseshop
        //later to be fixed to load images from any shop according $sOverrideShopId by implementing
        //function oxConfig::getShopURL($sOverrideShopId);

        $blNativeImg = $this->getConfigParam( 'blNativeImages' ) && $sOverrideShopId == $this->getShopId();

        if ( $this->isSsl() && !$blNoSsl ) {
            $sUrl = $blNativeImg ? $this->getSSLShopURL():$this->getConfigParam( 'sSSLShopURL');
        }

        if(!$sUrl) {
            $sUrl = $blNativeImg ? $this->getShopURL():$this->getConfigParam( 'sShopURL' );
        }

        $sLang = $this->getConfigParam( 'blUseDifferentDynDirs' )?oxLang::getInstance()->getTplLanguage():0;
        $sDir  = $this->_sPictureDir;

        $sImageDir = $sUrl . $this->formatOutDir($sDir, $iLang, false, true, $sOverrideShopId);

        oxUtils::getInstance()->toStaticCache($sCacheKey, $sImageDir);


        return $sImageDir;
        */
    }

    /**
     * Returns absolute path to Dyn images.
     *
     * @param string $sOverrideShopId Shop ID (default null)
     *
     * @deprecated
     *
     * @return string
     */
    public function getAbsDynImageDir( $sOverrideShopId = null )
    {
        return $this->getPictureDir(false);

        /*
        //$sShop = is_null($sOverrideShopId)?$this->getShopId():$sOverrideShopId;

        if (isset($this->_aAbsDynImageDir[$sShop]))
            return $this->_aAbsDynImageDir[$sShop];

        $sRoot = $this->getConfigParam('sShopDir');
        $iLang = $this->getConfigParam( 'blUseDifferentDynDirs' )?oxLang::getInstance()->getTplLanguage():0;
        $sDir  = $this->_sPictureDir;

        $this->_aAbsDynImageDir[$sShop] = $sRoot . $this->formatOutDir($sDir, $iLang, false, true, $sOverrideShopId);
        return $this->_aAbsDynImageDir[$sShop];
        */
    }

    /**
     * Returns shop template file path.
     *
     * @param string $sTemplate name of template file
     * @param bool   $blAdmin   mode - admin/non-admin (default is false)
     *
     * @deprecated
     *
     * @return string
     */
    public function getTemplateFile( $sTemplate, $blAdmin = false )
    {
        return $this->getTemplatePath( $sTemplate, $blAdmin );
    }

    /**
     * Returns path to template files. Eg. 'http://localhost/oxid/out/1/html/0/templates/'
     *
     * @param bool $blAdmin mode - admin/non-admin (default is false)
     *
     * @deprecated use getResourceUrl
     *
     * @return string
     */
    public function getBaseTemplateDir( $blAdmin = false )
    {
        return $this->getResourceUrl( null, $blAdmin );
        /*
        $blNativeImg = $this->getConfigParam( 'blNativeImages' ) && !$blAdmin;

        if ( $this->isSsl() ) {
            $sTemplateUrl = $blNativeImg?$this->getSSLShopURL():$this->getConfigParam( 'sSSLShopURL' );
        } else {
            $sTemplateUrl = $blNativeImg?$this->getShopURL():$this->getConfigParam( 'sShopURL' );
        }

        return $this->getResourceUrl( null, $blAdmin, $this->isSsl() );
        */
    }

    /**
     * Returns base path to template files. (eg. 'out/1/')
     *
     * @param bool $blAdmin mode - admin/non-admin (default is false)
     *
     * @deprecated
     *
     * @return string
     */
    public function getBaseTplDir( $blAdmin = false )
    {
        //TODO: check usage
        return $this->getResourceUrl( null, $blAdmin);
    }

    /**
     * Returns array of available currencies
     *
     * @param integer $iCurrency Active currency number (default null)
     *
     * @return array
     */
    public function getCurrencyArray( $iCurrency = null )
    {
        $aConfCurrencies = $this->getConfigParam( 'aCurrencies' );
        if ( !is_array( $aConfCurrencies ) ) {
            return array();
        }

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modConfig::$unitMOD ) && is_object( modConfig::$unitMOD ) ) {
                try{
                    $aAltCurrencies = modConfig::getInstance()->getConfigParam( 'modaCurrencies' );
                    if ( isset( $aAltCurrencies ) ) {
                        $aConfCurrencies = $aAltCurrencies;
                    }
                } catch( Exception $e ) {
                    // if exception is thrown, use default
                }
            }
        }

        // processing currency configuration data
        $aCurrencies = array();
        reset( $aConfCurrencies );
        while ( list( $key, $val ) = each( $aConfCurrencies ) ) {
            if ( $val ) {
                $oCur = new oxStdClass();
                $oCur->id      = $key;
                $sCur = explode( '@', $val);
                $oCur->name     = trim( $sCur[0] );
                $oCur->rate     = trim( $sCur[1] );
                $oCur->dec      = trim( $sCur[2] );
                $oCur->thousand = trim( $sCur[3] );
                $oCur->sign     = trim( $sCur[4] );
                $oCur->decimal  = trim( $sCur[5] );

                // change for US version
                if ( isset( $sCur[6] ) ) {
                    $oCur->side = trim($sCur[6]);
                }

                if ( isset( $iCurrency) && $key == $iCurrency ) {
                    $oCur->selected = 1;
                } else {
                    $oCur->selected = 0;
                }
                $aCurrencies[$key]= $oCur;
            }

            // #861C -  performance, do not load other currencies
            if ( !$this->getConfigParam( 'bl_perfLoadCurrency' ) ) {
                break;
            }
        }
        return $aCurrencies;
    }

    /**
     * Returns currency object.
     *
     * @param string $sName Name of active currency
     *
     * @return object
     */
    public function getCurrencyObject( $sName )
    {
        $aSearch = $this->getCurrencyArray();
        foreach ( $aSearch as $oCur ) {
            if ( $oCur->name == $sName ) {
                return $oCur;
            }
        }
    }

    /**
     * Checks if the shop is in demo mode.
     *
     * @return bool
     */
    public function isDemoShop()
    {
        return $this->getConfigParam('blDemoShop');
    }

    /**
     * Returns OXID eShop edition
     *
     * @return string
     */
    public function getEdition()
    {
            return "CE";


    }

    /**
     * Returns full eShop edition name
     *
     * @return string
     */
    public function getFullEdition()
    {
        $sEdition = $this->getEdition();

            if ($sEdition == "CE") {
                return "Community Edition";
            }



        return $sEdition;
    }

    /**
     * Returns shops version number (eg. '4.0.0.0')
     *
     * @return unknown
     */
    public function getVersion()
    {
        $sVersion = $this->getActiveShop()->oxshops__oxversion->value;
        return $sVersion;
    }

    /**
     * Returns build revision number or false on read error.
     *
     * @return int
     */
    public function getRevision()
    {
        try {
            $sFileName = getShopBasePath() . "/pkg.rev";
            $iRev = (int) trim(@file_get_contents($sFileName));
        } catch (Exception $e) {
            return false;
        }

        if (!$iRev) {
            return false;
        }

        return $iRev;
    }


    /**
     * Checks if shop is MALL. Returns true on success.
     *
     * @return bool
     */
    public function isMall()
    {

            return false;
    }

    /**
     * Checks version of shop, returns:
     *  0 - version is bellow 2.2
     *  1 - Demo or unlicensed
     *  2 - Pro
     *  3 - Enterprise
     *
     * @return int
     */
    public function detectVersion()
    {
    }



    /**
     * Updates or adds new shop configuration parameters to DB.
     *
     * @param string $sVarType Variable Type
     * @param string $sVarName Variable name
     * @param string $sVarVal  Variable value
     * @param string $sShopId  Shop ID, default is current shop
     *
     * @return null
     */
    public function saveShopConfVar( $sVarType, $sVarName, $sVarVal, $sShopId = null )
    {
        if ( !$sShopId ) {
            $sShopId = $this->getShopId();
        }

        $oDb = oxDb::getDb();
        $sQ = "delete from oxconfig where oxshopid = '$sShopId' and oxvarname = '$sVarName'";
        $oDb->execute( $sQ );
        $sUid = oxUtilsObject::getInstance()->generateUID();

        $sUid     = mysql_real_escape_string($sUid);
        $sShopId  = mysql_real_escape_string($sShopId);
        $sVarName = mysql_real_escape_string($sVarName);
        $sVarVal  = mysql_real_escape_string($sVarVal);

        $sQ = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
               values('$sUid', '$sShopId', '$sVarName', '$sVarType', ENCODE( '$sVarVal', '".$this->getConfigParam('sConfigKey')."'))";

        $oDb->execute( $sQ );
    }

    /**
     * Retrieves shop configuration parameters from DB.
     *
     * @param string $sVarName Variable name
     * @param string $sShopId  Shop ID
     *
     * @return object - raw configuration value in DB
     */
    public function getShopConfVar( $sVarName, $sShopId = null )
    {
        if ( !$sShopId ) {
            $sShopId = $this->getShopId();
        }

        $sQ  = "select oxvartype, DECODE( oxvarvalue, '".$this->getConfigParam( 'sConfigKey' )."') as oxvarvalue from oxconfig where oxshopid = '$sShopId' and oxvarname = '$sVarName'";
        $oRs = oxDb::getDb(true)->Execute( $sQ );

        $sValue = null;
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            $sVarType = $oRs->fields['oxvartype'];
            $sVarVal  = $oRs->fields['oxvarvalue'];
            switch ( $sVarType ) {
                case 'arr':
                case 'aarr':
                    $sValue =  unserialize( $sVarVal );
                    break;
                case 'bool':
                    $sValue =  ( $sVarVal == 'true' || $sVarVal == '1' );
                    break;
                default:
                    $sValue = $sVarVal;
                }
        }
        return $sValue;
    }

    /**
     * Returns true if current active shop is in productive mode or false if not
     *
     * @return bool
     */
    public function isProductiveMode()
    {
        $blProductive = false;

        $blProductive = $this->getConfigParam( 'blProductive' );
        if ( !isset( $blProductive ) ) {
            $sQ = 'select oxproductive from oxshops where oxid = "'.$this->getShopId().'"';
            $blProductive = ( bool ) oxDb::getDb()->getOne( $sQ );
            $this->setConfigParam( 'blProductive', $blProductive );
        }

        return $blProductive;
    }



    /**
     * Function returns default shop ID
     *
     * @return string
     */
    public function getBaseShopId()
    {

            return 'oxbaseshop';
    }

    /**
     * Loads and returns active shop object
     *
     * @return oxshop
     */
    public function getActiveShop()
    {
        if ( $this->_oActShop && $this->_iShopId == $this->_oActShop->getId() &&
             $this->_oActShop->getLanguage() == oxLang::getInstance()->getBaseLanguage() ) {
            return $this->_oActShop;
        }

        $this->_oActShop = oxNew( 'oxshop' );
        $this->_oActShop->load( $this->getShopId() );
        return $this->_oActShop;
    }

    /**
     * Returns active view object. If this object was not defined - returns oxview object
     *
     * @return oxview
     */
    public function getActiveView()
    {
        if ( $this->_oActView != null ) {
            return $this->_oActView;
        }

        $this->_oActView = oxNew( 'oxubase' );
        return $this->_oActView;
    }

    /**
     * View object setter
     *
     * @param object $oView view object
     *
     * @return null
     */
    public function setActiveView( $oView )
    {
        $this->_oActView = $oView;
    }

    /**
     * Returns true if current installation works in UTF8 mode, or false if not
     *
     * @return bool
     */
    public function isUtf()
    {
        return ( bool ) $this->getConfigParam( 'iUtfMode' );
    }
}
