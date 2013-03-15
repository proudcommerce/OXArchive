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
 * @copyright (C) OXID eSales AG 2003-2010
 * @version OXID eShop CE
 * @version   SVN: $Id: article_list.php 26619 2010-03-17 13:44:29Z arvydas $
 */

/**
 * Admin article list manager.
 * Collects base article information (according to filtering rules), performs sorting,
 * deletion of articles, etc.
 * Admin Menu: Manage Products -> Articles.
 * @package admin
 */
class Article_List extends oxAdminList
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxarticle';

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxarticlelist';

    /**
     * Collects articles base data and passes them according to filtering rules,
     * returns name of template file "article_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();
        $sPwrSearchFld = oxConfig::getParameter( "pwrsearchfld" );
        if ( !isset( $sPwrSearchFld ) ) {
            $sPwrSearchFld  = "oxtitle";
        }

        $oArticle = null;
        $oList = $this->getItemList();
        if ( $oList) {
            foreach ( $oList as $key => $oArticle ) {
                $sFieldName = "oxarticles__".strtolower( $sPwrSearchFld );

                // formatting view
                if ( !$myConfig->getConfigParam( 'blSkipFormatConversion' ) ) {
                    if ( $oArticle->$sFieldName->fldtype == "datetime" )
                        oxDb::getInstance()->convertDBDateTime( $oArticle->$sFieldName );
                    elseif ( $oArticle->$sFieldName->fldtype == "timestamp" )
                        oxDb::getInstance()->convertDBTimestamp( $oArticle->$sFieldName );
                    elseif ( $oArticle->$sFieldName->fldtype == "date" )
                        oxDb::getInstance()->convertDBDate( $oArticle->$sFieldName );
                }

                $oArticle->pwrsearchval = $oArticle->$sFieldName->value;
                $oList[$key] = $oArticle;
            }
        }

        parent::render();

        // load fields
        if ( !$oArticle && $oList ) {
            $oArticle = $oList->getBaseObject();
        }
        $this->_aViewData["pwrsearchfields"] = $oArticle ? $oArticle->getSearchableFields() : null;
        $this->_aViewData["pwrsearchfld"]    = strtoupper( $sPwrSearchFld );

        if ( isset( $this->_aViewData["where"] ) ) {
            $sFieldName = "oxarticles__".strtoupper( $sPwrSearchFld );
            if ( isset( $this->_aViewData["where"]->$sFieldName ) ) {
                $this->_aViewData["pwrsearchinput"] = $this->_aViewData["where"]->$sFieldName;
            }
        }

        $sType  = '';
        $sValue = '';

        $sArtCat= oxConfig::getParameter( "art_category" );
        if ( $sArtCat && strstr( $sArtCat, "@@" ) !== false ) {
            list( $sType, $sValue ) = explode( "@@", $sArtCat );
        }

        // parent categorie tree
        $oCatTree = oxNew( "oxCategoryList");
        $oCatTree->buildList( $myConfig->getConfigParam( 'bl_perfLoadCatTree' ) );
        if ( $sType === 'cat' ) {
            foreach ($oCatTree as $oCategory ) {
                if ( $oCategory->oxcategories__oxid->value == $sValue ) {
                    $oCategory->selected = 1;
                    break;
                }
            }
        }
        $this->_aViewData["cattree"] = $oCatTree;

       // manufacturer list
        $oMnfTree = oxNew( "oxManufacturerList");
        $oMnfTree->loadManufacturerList();
        if ( $sType === 'mnf' ) {
            foreach ($oMnfTree as $oManufacturer ) {
                if ( $oManufacturer->oxmanufacturers__oxid->value == $sValue ) {
                    $oManufacturer->selected = 1;
                    break;
                }
            }
        }
        $this->_aViewData["mnftree"] = $oMnfTree;

        // vendor list
        $oVndTree = oxNew( "oxVendorList");
        $oVndTree->loadVendorList();
        if ( $sType === 'vnd' ) {
            foreach ($oVndTree as $oVendor ) {
                if ( $oVendor->oxvendor__oxid->value == $sValue ) {
                    $oVendor->selected = 1;
                    break;
                }
            }
        }
        $this->_aViewData["vndtree"] = $oVndTree;

        return "article_list.tpl";
    }

    /**
     * Sets articles sorting by category.
     *
     * @param string $sSql sql string
     *
     * @return string
     */
    protected function _changeselect( $sSql )
    {
        $sArtCat= oxConfig::getParameter("art_category");
        if ( $sArtCat && strstr($sArtCat, "@@") !== false ) {
            list($sType, $sValue) = explode("@@", $sArtCat);
        }

        $sTable   = getViewName( "oxarticles" );
        switch ($sType) {
            // add category
            case 'cat':
                $sO2CView = getViewName( "oxobject2category" );
                $sInsert  = "from $sTable left join $sO2CView on $sTable.oxid = $sO2CView.oxobjectid where $sO2CView.oxcatnid = ".oxDb::getDb()->quote($sValue)." and ";
                $sSql = preg_replace( "/from\s+$sTable\s+where/i", $sInsert, $sSql);
                break;
            // add category
            case 'mnf':
                $sSql.= " and $sTable.oxmanufacturerid = ".oxDb::getDb()->quote($sValue);
                break;
            // add vendor
            case 'vnd':
                $sSql.= " and $sTable.oxvendorid = ".oxDb::getDb()->quote($sValue);
                break;
        }
        return $sSql;
    }

    /**
     * Builds and returns array of SQL WHERE conditions.
     *
     * @return array
     */
    public function buildWhere()
    {
        // we override this to select only parent articles
        $this->_aWhere = ( array ) parent::buildWhere();

        // adding folder check
        $sFolder = oxConfig::getParameter( 'folder' );
        if ( $sFolder && $sFolder != '-1' ) {
            $sViewName = getViewName( 'oxarticles' );
            $this->_aWhere["$sViewName.oxfolder"] = $sFolder;
        }

        return $this->_aWhere;
    }

    /**
     * Adding empty parent check
     *
     * @param array  $aWhere SQL condition array
     * @param string $sQ     SQL query string
     *
     * @return $sQ
     */
    protected function _prepareWhereQuery( $aWhere, $sQ )
    {
        $sQ = parent::_prepareWhereQuery( $aWhere, $sQ );

        return $sQ . " and ".getViewName( 'oxarticles' ).".oxparentid = '' ";
    }

    /**
     * Deletes entry from the database
     *
     * @return null
     */
    public function deleteEntry()
    {
        $sOxId = oxConfig::getParameter( "oxid" );
        $oArticle = oxNew( "oxarticle");
        if ( $sOxId && $oArticle->load( $sOxId ) ) {
            parent::deleteEntry();
        }
    }

}
