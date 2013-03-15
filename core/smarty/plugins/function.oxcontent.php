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
 * @package smartyPlugins
 * @copyright (C) OXID eSales AG 2003-2009
 * @version OXID eShop CE
 * $Id: function.oxcontent.php 22213 2009-09-08 08:22:51Z sarunas $
 */

/*
* Smarty plugin
* -------------------------------------------------------------
* File: insert.oxid_content.php
* Type: string, html
* Name: oxid_content
* Purpose: Output content snippet
* add [{ insert name="oxid_content" ident="..." }] where you want to display content
* -------------------------------------------------------------
*/
function smarty_function_oxcontent( $params, &$smarty )
{
    $myConfig = oxConfig::getInstance();
    $sText = $myConfig->getActiveShop()->oxshops__oxproductive->value ? null : "<b>content not found ! check ident(".$params['ident'].") !</b>";
    $smarty->oxidcache = new oxField($sText, oxField::T_RAW);

    $sIdent = isset( $params['ident'] )?$params['ident']:null;
    $sOxid  = isset( $params['oxid'] )?$params['oxid']:null;

    if( $sIdent || $sOxid ) {
        $oContent = oxNew( "oxcontent" );
        if ( $sOxid ) {
            $blLoaded = $oContent->load( $sOxid );
        } else {
            $blLoaded = $oContent->loadbyIdent( $sIdent );
        }

        if ( $blLoaded && $oContent->oxcontents__oxactive->value ) {
            // set value
            $sField = "oxcontent";
            if ( $params['field'] ) {
                $sField = $params['field'];
            }
            // set value
            $sProp = 'oxcontents__'.$sField;
            $smarty->oxidcache = clone $oContent->$sProp;
            $smarty->compile_check  = true;
            $sCacheId = oxLang::getInstance()->getBaseLanguage().(int) oxConfig::getParameter( 'currency' );
            $sText = $smarty->fetch( "ox:".$params['ident'].$sField.$sCacheId);
            $smarty->compile_check  = $myConfig->getConfigParam( 'blCheckTemplates' );
        }
    }

    // if we write '[{oxcontent ident="oxfirststart" assign="fs_text"}]' the content wont be outputed.
    // instead of this the content will be assignet to variable.
    if( isset( $params['assign']) && $params['assign'])
        $smarty->assign($params['assign'], $sText);
    else
        return $sText;

}
