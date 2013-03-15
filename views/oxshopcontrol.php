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
 * $Id: oxshopcontrol.php 22590 2009-09-24 06:24:00Z alfonsas $
 */

/**
 * Main shop actions controller. Processes user actions, logs
 * them (if needed), controlls output, redirects according to
 * processed methods logic. This class is initalized from index.php
 */
class oxShopControl extends oxSuperCfg
{
    /**
     * Main shop manager, that sets shop status, executes configuration methods.
     * Executes oxShopControl::_runOnce(), if needed sets default class (according
     * to admin or regular activities).
     *
     * Session variables:
     * <b>actshop</b>
     *
     * @return null
     */
    public function start()
    {
        $myConfig = $this->getConfig();

        //perform tasks once per session
        $this->_runOnce();

        $sClass    = oxConfig::getParameter( 'cl' );
        $sFunction = oxConfig::getParameter( 'fnc' );

        if ( !$sClass ) {

            if ( !$this->isAdmin() ) {

                // first start of the shop
                // check wether we have to display mall startscreen or not
                if ( $myConfig->isMall() ) {

                    $iShopCount = oxDb::getDb()->getOne( 'select count(*) from oxshops where oxactive = 1' );

                    $sMallShopURL = $myConfig->getConfigParam( 'sMallShopURL' );
                    if ( $iShopCount && $iShopCount > 1 && $myConfig->getConfigParam( 'iMallMode' ) != 0 && !$sMallShopURL ) {
                        // no class specified so we need to change back to baseshop
                        $sClass = 'mallstart';
                    }
                }

                if ( !$sClass ) {
                    $sClass = 'start';
                }
            } else {
                $sClass = 'login';
            }

            oxSession::setVar( 'cl', $sClass );
        }

        // baseshop always active if there is no mall
        if ( !oxSession::getVar( 'actshop' ) ) {
            oxSession::setVar( 'actshop', $myConfig->getShopId() );
        }

        try {
            $this->_process( $sClass, $sFunction );
        } catch( oxSystemComponentException $oEx ) {
            //possible reason: class does not exist etc. --> just redirect to start page
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
            oxUtils::getInstance()->redirect( $myConfig->getShopHomeURL() .'cl=start' );
        } catch ( oxCookieException $oEx ) {
            // redirect to start page and display the error
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
            oxUtils::getInstance()->redirect( $myConfig->getShopHomeURL() .'cl=start' );
        }
    }

    /**
     * Logs user performad actions to DB. Skips action loggin if
     * it's search engine.
     *
     * @param string $sClass Name of class
     * @param srring $sFnc   Name of executed class method
     *
     * @return null
     */
    protected function _log( $sClass, $sFnc )
    {
        $oDb = oxDb::getDb();
        $sShopID    = oxSession::getVar( 'actshop' );
        $sTime      = date( 'Y-m-d H:i:s' );
        $sSidQuoted       = $oDb->quote( $this->getSession()->getId() );
        $sUserIDQuoted    = $oDb->quote( oxSession::getVar( 'usr' ) );
        $sCnid      = oxConfig::getParameter( 'cnid' );
        $sAnid      = oxConfig::getParameter( 'aid' )?oxConfig::getParameter( 'aid' ):oxConfig::getParameter( 'anid' );
        $sParameter = '';

        if ( $sClass == 'info' ) {
            $sParameter = str_replace( '.tpl', '', oxConfig::getParameter('tpl') );
        } elseif ( $sClass == 'search' ) {
            $sParameter = oxConfig::getParameter( 'searchparam' );
        }

        $sFncQuoted = $oDb->quote( $sFnc );
        $sClassQuoted = $oDb->quote( $sClass );
        $sParameterQuoted = $oDb->quote( $sParameter );

        $oDb->execute( "insert into oxlogs (oxtime, oxshopid, oxuserid, oxsessid, oxclass, oxfnc, oxcnid, oxanid, oxparameter)
                                 values( '$sTime', '$sShopID', $sUserIDQuoted, $sSidQuoted, $sClassQuoted, $sFncQuoted, '$sCnid', '$sAnid', $sParameterQuoted )" );
    }

    // OXID : add timing
    /**
     * Starts resource monitor
     *
     * @return null
     */
    protected function _startMonitor()
    {
        if ( !$this->isAdmin() && $this->getConfig()->getConfigParam( 'iDebug' ) ) {
            list ( $sUsec, $sSec ) = explode( ' ', microtime() );
            $this->dTimeStart = ( ( float ) $sUsec + ( float ) $sSec );
        }
    }

    /**
     * Stops resource monitor, summarizes and outputs values
     *
     * @param bool  $blIsCache  Is content cache
     * @param bool  $blIsCached Is content cached
     * @param bool  $sViewID    View ID
     * @param array $aViewData  View data
     *
     * @return null
     */
    protected function _stopMonitor( $blIsCache = false, $blIsCached = false, $sViewID = null, $aViewData = array() )
    {
        $myConfig = $this->getConfig();
        if ( !$this->isAdmin() && $myConfig->getConfigParam( 'iDebug' ) != 0 ) {
            echo '<div align="left">';

            // outputting template params
            if ( $myConfig->getConfigParam( 'iDebug' ) == 4 ) {

                reset( $aViewData );
                while ( list( $sViewName, $oViewData ) =each( $aViewData ) ) {
                    // show debbuging information
                        echo( "TemplateData[$sViewName] : <br />\n");
                        print_r( $oViewData );
                }
            }

            // output timing
            list( $sUsec, $sSec ) = explode( ' ', microtime() );
            $this->dTimeEnd = ( ( float ) $sUsec + ( float ) $sSec );
            $dTimeSpent = $this->dTimeEnd - $this->dTimeStart;

            echo 'Execution time :'.$dTimeSpent.'<br />';

            // memory usage info
            if ( function_exists( 'memory_get_usage' ) ) {
                $iKb = ( int ) ( memory_get_usage() / 1024 );
                $iMb = round($iKb / 1024, 3);
                echo 'Memory usage: '.$iMb.' MB';

                if ( function_exists( 'memory_get_peak_usage' ) ) {
                    $iPeakKb = ( int ) ( memory_get_peak_usage() / 1024 );
                    $iPeakMb = round($iPeakKb / 1024, 3);
                    echo ' (peak: '.$iPeakMb.' MB)';
                }
                echo '<br />';

                if ( version_compare( PHP_VERSION, '5.2.0', '>=' ) ) {
                    $iKb = ( int ) ( memory_get_usage( true ) / 1024 );
                    $iMb = round($iKb / 1024, 3);
                    echo 'System memory usage: '.$iMb.' MB';

                    if ( function_exists( 'memory_get_peak_usage' ) ) {
                        $iPeakKb = ( int ) ( memory_get_peak_usage( true ) / 1024 );
                        $iPeakMb = round($iPeakKb / 1024, 3);
                        echo ' (peak: '.$iPeakMb.' MB)';
                    }
                    echo '<br />';
                }
            }

            echo '</div>';
        }

    }

    /**
     * Initiates object (object::init()), executes passed function
     * (oxShopControl::executeFunction(), if method returns some string - will
     * redirect page and will call another function according to returned
     * parameters), renders object (object::render()). Performs output processing
     * oxOutput::ProcessViewArray(). Passes template variables to template
     * engine witch generates output. Output is additionally processed
     * (oxOutput::Process()), fixed links according search engines optimization
     * rules (configurable in Admin area). Finally echoes the output.
     *
     * @param string $sClass    Name of class
     * @param string $sFunction Name of function
     *
     * @return null
     */
    protected function _process( $sClass, $sFunction )
    {
        $myConfig = $this->getConfig();

        if ( !oxUtils::getInstance()->isSearchEngine() &&
             !( $this->isAdmin() || !$myConfig->getConfigParam( 'blLogging' ) ) ) {
            $this->_log( $sClass, $sFunction );
        }

        // starting resource monitor
        $this->_startMonitor();

        // creating current view object
        $oViewObject = oxNew( $sClass );

        // store this call
        $oViewObject->setClassName( $sClass );
        $oViewObject->setFncName( $sFunction );

        $myConfig->setActiveView( $oViewObject );

        // caching params ...
        $sOutput      = null;
        $blIsCached   = false;
        $blIsCachable = false;


        // init class
        $oViewObject->init();

        // executing user defined function
        $oViewObject->executeFunction( $oViewObject->getFncName() );


        // get Smarty is important here as it sets template directory correct
        $oSmarty = oxUtilsView::getInstance()->getSmarty();

        // if no cache was stored before we should generate it
        if ( !$blIsCached ) {

            // render it
            $sTemplateName = $oViewObject->render();

            // check if template dir exists
            $sTemplateFile = $myConfig->getTemplatePath( $sTemplateName, $this->isAdmin() ) ;
            if ( !file_exists( $sTemplateFile)) {
                $oEx = oxNew( 'oxSystemComponentException' );
                $oLang = oxLang::getInstance();
                $oEx->setMessage( sprintf($oLang->translateString( 'EXCEPTION_SYSTEMCOMPONENT_TEMPLATENOTFOUND', $oLang->getBaseLanguage() ), $sTemplateFile) );
                $oEx->setComponent( $sTemplateName );
                throw $oEx;
            }
            $aViewData = $oViewObject->getViewData();

            //Output processing. This is useful for modules. As sometimes you may want to process output manually.
            $oOutput = oxNew( 'oxoutput' );
            $aViewData = $oOutput->processViewArray( $aViewData, $oViewObject->getClassName() );
            $oViewObject->setViewData( $aViewData );

            //add all exceptions to display
            if ( ( $aErrors = oxSession::getVar( 'Errors' ) ) ) {
                oxUtilsView::getInstance()->passAllErrorsToView( $aViewData, $aErrors );

                // resetting errors after displaying them
                oxSession::setVar( 'Errors', array() );
            }

            foreach ( array_keys( $aViewData ) as $sViewName ) {
                $oSmarty->assign_by_ref( $sViewName, $aViewData[$sViewName] );
            }

            // passing current view object to smarty
            $oSmarty->oxobject = $oViewObject;


            $sOutput = $oSmarty->fetch( $sTemplateName, $oViewObject->getViewId() );

            //Output processing - useful for modules as sometimes you may want to process output manually.
            $sOutput = $oOutput->process( $sOutput, $oViewObject->getClassName() );
            $sOutput = $oOutput->addVersionTags( $sOutput );
        }


        // show output
        //ob_Start("gzip");

        // #M1047 Firefox duplicated GET fix
        header("Content-Type: text/html; charset=".oxLang::getInstance()->translateString( 'charset' ));
        echo ( $sOutput );

        $myConfig->pageClose();

        // stopping resource monitor
        $this->_stopMonitor( $blIsCachable, $blIsCached, $sViewID, $oViewObject->getViewData() );
    }

    /**
     * This function is only executed one time here we perform checks if we
     * only need once per session
     *
     * @return null
     */
    protected function _runOnce()
    {
        $myConfig = $this->getConfig();
        $blRunOnceExecuted = oxSession::getVar( 'blRunOnceExecuted' );
        $blProductive = true;


            // A. is it the right place for this code ?
            // productive mode ?
            if ( ! ( $blProductive = $myConfig->isProductiveMode() ) ) {
                if ( is_null($myConfig->getConfigParam( 'iDebug' )) ) {
                    $myConfig->setConfigParam( 'iDebug', -1 );
                }

                    error_reporting( E_ALL ^ E_NOTICE );
            } else {

                // disable error logging if server is misconfigured
                if ( !ini_get( 'log_errors' ) ) {
                    error_reporting( E_NONE );
                } else {
                        error_reporting( E_ALL ^ E_NOTICE );
                }
            }



        if ( !$blRunOnceExecuted && !$this->isAdmin() && $blProductive ) {

            // perform stuff - check if setup is still there
            if ( file_exists( $myConfig->getConfigParam( 'sShopDir' ) . '/setup/index.php' ) ) {
                $oActView = oxNew( 'oxubase' );
                $oSmarty = oxUtilsView::getInstance()->getSmarty();
                $oSmarty->assign('oView', $oActView);
                $oSmarty->assign('oViewConf', $oActView->getViewConfig());
                oxUtils::getInstance()->showMessageAndExit( $oSmarty->fetch( 'err_setup.tpl' ) );
            }

            oxSession::setVar( 'blRunOnceExecuted', true );
        }
    }
}
