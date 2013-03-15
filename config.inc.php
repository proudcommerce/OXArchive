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
 * @package   main
 * @copyright (C) OXID eSales AG 2003-2011
 * @version OXID eShop CE
 */

    /** @name database information */
        $this->dbHost = '<dbHost_ce>'; // database host name
        $this->dbName = '<dbName_ce>'; // database name
        $this->dbUser = '<dbUser_ce>'; // database user name
        $this->dbPwd  = '<dbPwd_ce>'; // database user password
        $this->dbType = 'mysql';
        $this->sShopURL     = '<sShopURL_ce>'; // eShop base url, required
        $this->sSSLShopURL  = null;            // eShop SSL url, optional
        $this->sAdminSSLURL = null;            // eShop Admin SSL url, optional
        $this->sShopDir     = '<sShopDir_ce>';
        $this->sCompileDir  = '<sCompileDir_ce>';

    // Template theme name, a directory in out/ folder containing all needed resources
    $this->sTheme = 'basic';

    // Custom  theme, a directory in out/ folder containing only modified template files
    $this->sCustomTheme = null;

    // Uncoment only for former (pre version 4) template compatibility
    // $this->blFormerTplSupport = true;
    // $this->blFixedWidthLayout = true;

    // UTF-8 mode in shop 0 - off, 1 - on
    $this->iUtfMode  = '<iUtfMode>';

    // File type whitelist for file upload
    $this->aAllowedUploadTypes = array('jpg', 'gif', 'png', 'pdf', 'mp3', 'avi', 'mpg', 'mpeg', 'doc', 'xls', 'ppt');

    // timezone information
    date_default_timezone_set('Europe/Berlin');

    // Search engine friendly URL processor
    // After changing this value, you should rename oxid.php file as well
    // Always leave .php extension here unless you know what you are doing
    $this->sOXIDPHP = "oxid.php";

    //  enable debug mode for template development or bugfixing
    // -1 = Logger Messages internal use only
    //  0 = off
    //  1 = smarty
    //  2 = SQL
    //  3 = SQL + smarty
    //  4 = SQL + smarty + shoptemplate data
    //  5 = Delivery Cost calculation info
    //  6 = SMTP Debug Messages
    //  7 = oxDbDebug SQL parser
    $this->iDebug = 0;

    // Log all modifications performed in Admin
    $this->blLogChangesInAdmin = false;

    // Force admin email
    $this->sAdminEmail = '';

    // in case session must be started on first user page visit (not only on
    // session required action) set this option value 1
    $this->blForceSessionStart = false;

    // Use browser cookies to store session id (no sid parameter in URL)
    $this->blSessionUseCookies = true;

    // The domain that the cookie is available: array( _SHOP_ID_ => _DOMAIN_ );
    // check setcookie() documentation for more details @php.net
    $this->aCookieDomains = null;

    // The path on the server in which the cookie will be available on: array( _SHOP_ID_ => _PATH_ );
    // check setcookie() documentation for more details @php.net
    $this->aCookiePaths = null;

    // uncomment the following line if you want to leave euro sign unchanged in output
    // by default is set to convert euro sign symbol to html entity
    // $this->blSkipEuroReplace = true;


    // List of all Search-Engine Robots
    $this->aRobots = array(
                        'googlebot',
                        'ultraseek',
                        'crawl',
                        'spider',
                        'fireball',
                        'robot',
                        'spider',
                        'robot',
                        'slurp',
                        'fast',
                        'altavista',
                        'teoma',
                        );

    // Deactivate Static URL's for these Robots
    $this->aRobotsExcept = array();

    // IP addresses for which session/cookie id match and user agent change checks are off
    $this->aTrustedIPs = array();

    // disable using and loading the gmp extension for the OpenId library
    // as using the dl() function causes problems with suhosin.
    define('Auth_OpenID_BUGGY_GMP', true);

    /**
     * Works only if basket reservations feature is enabled in admin.
     *
     * The number specifies how many expired basket reservations are
     * cleaned per one request (to the eShop).
     * Cleaning a reservation basically means returning the reserved
     * stock to the articles.
     *
     * Keeping this number too low may cause article stock being returned too
     * slowly, while too high value may have spiking impact on the performance.
     */
    $this->iBasketReservationCleanPerRequest = 200;

    // Trusted shops buyer protection wsdl url
    $this->sTsProtectionUrl = "https://protection.trustedshops.com/ts/protectionservices/ApplicationRequestService?wsdl";
    // This is only needed for testing during integration
    $this->sTsTestProtectionUrl = "https://protection-qa.trustedshops.com/ts/protectionservices/ApplicationRequestService?wsdl";

    // Trusted Shops Ratings login info
    // Do not change credentials unless instructed otherwise by Trusted Shops!
    $this->sTsUser = "oxid_esales";
    $this->sTsPass = "V1AoGEXm";

    // Trusted Shops Ratings configuration array
    $this->aTsConfig = array( "blTestMode"   => false, // set TRUE to enable testing mode
                              "sTsUrl"       => "https://www.trustedshops.com", // Trusted Shops Rating main url
                              "sTsTestUrl"   => "https://qa.trustedshops.com",  // Trusted Shops Rating test url
                              "sTsWidgetUri" => array( "bewertung/widget/widgets/%s.gif" ), // rating widget url
                              "sTsInfoUri"   => array( "de" => "bewertung/info_%s.html",  // DE rating info url
                                                       "en" => "buyerrating/info_%s.html" // EN rating info url
                                                     ),
                              "sTsRatingUri" => array( "de" => "bewertung/bewerten_%s.html", // DE rating url
                                                       "en" => "buyerrating/rate_%s.html"    // EN rating url
                                                     )
                             );
    // Trusted Shops Ratings service wsdl
    $this->sTsServiceWsdl = "https://www.trustedshops.de/ts/services/TsRating?wsdl";

    // Trusted Shops Ratings test service wsdl
    $this->sTsServiceTestWsdl = "https://qa.trustedshops.de/ts/services/TsRating?wsdl";