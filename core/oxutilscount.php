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
 * $Id: oxutilscount.php 18025 2009-04-09 11:30:19Z arvydas $
 */

/**
 * Date manipulation utility class
 */
class oxUtilsCount extends oxSuperCfg
{
    /**
     * oxUtils class instance.
     *
     * @var oxutils* instance
     */
    private static $_instance = null;

    /**
     * Users view id, used to identify current state cache
     *
     * @var string
     */
    protected $_sUserViewId = null;

    /**
     * Returns object instance
     *
     * @return oxUtilsCount
     */
    public static function getInstance()
    {
        // disable caching for test modules
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            static $inst = array();
            self::$_instance = $inst[oxClassCacheKey()];
        }

        if ( !self::$_instance instanceof oxUtilsCount ) {

            self::$_instance = oxNew( 'oxUtilsCount' );
            if ( defined( 'OXID_PHP_UNIT' ) ) {
                $inst[oxClassCacheKey()] = self::$_instance;
            }
        }
        return self::$_instance;
    }

    /**
     * Returns category article count
     *
     * @param string $sCatId Category Id
     *
     * @return int
     */
    public function getCatArticleCount( $sCatId )
    {
        // current status unique ident
        $sActIdent = $this->_getUserViewId();

        // loading from cache
        $aCatData = $this->_getCatCache();

        if ( !$aCatData || !isset( $aCatData[$sCatId][$sActIdent] ) ) {
            $iCnt = $this->setCatArticleCount( $aCatData, $sCatId, $sActIdent );
        } else {
            $iCnt = $aCatData[$sCatId][$sActIdent];
        }
        return $iCnt;
    }

    /**
     * Returns category article count price
     *
     * @param string $sCatId     Category Id
     * @param double $dPriceFrom from price
     * @param double $dPriceTo   to price
     *
     * @return int
     */
    public function getPriceCatArticleCount( $sCatId, $dPriceFrom, $dPriceTo )
    {
        // current status unique ident
        $sActIdent = $this->_getUserViewId();

        // loading from cache
        $aCatData = $this->_getCatCache();

        if ( !$aCatData || !isset( $aCatData[$sCatId][$sActIdent] ) ) {
            $iCnt = $this->setPriceCatArticleCount( $aCatData, $sCatId, $sActIdent, $dPriceFrom, $dPriceTo );
        } else {
            $iCnt = $aCatData[$sCatId][$sActIdent];
        }
        return $iCnt;
    }

    /**
     * Returns vendor article count
     *
     * @param string $sVendorId Vendor category Id
     *
     * @return int
     */
    public function getVendorArticleCount( $sVendorId )
    {
        // current category unique ident
        $sActIdent = $this->_getUserViewId();

        // loading from cache
        $aVendorData = $this->_getVendorCache();

        if ( !$aVendorData || !isset( $aVendorData[$sVendorId][$sActIdent] ) ) {
            $iCnt = $this->setVendorArticleCount( $aVendorData, $sVendorId, $sActIdent );
        } else {
            $iCnt = $aVendorData[$sVendorId][$sActIdent];
        }
        return $iCnt;
    }

    /**
     * Returns Manufacturer article count
     *
     * @param string $sManufacturerId Manufacturer category Id
     *
     * @return int
     */
    public function getManufacturerArticleCount( $sManufacturerId )
    {
        // current category unique ident
        $sActIdent = $this->_getUserViewId();

        // loading from cache
        $aManufacturerData = $this->_getManufacturerCache();

        if ( !$aManufacturerData || !isset( $aManufacturerData[$sManufacturerId][$sActIdent] ) ) {
            $iCnt = $this->setManufacturerArticleCount( $aManufacturerData, $sManufacturerId, $sActIdent );
        } else {
            $iCnt = $aManufacturerData[$sManufacturerId][$sActIdent];
        }
        return $iCnt;
    }

    /**
     * Saves and returns category article count into cache
     *
     * @param array  $aCache    Category cache data
     * @param string $sCatId    Unique category ident
     * @param string $sActIdent ID
     *
     * @return int
     */
    public function setCatArticleCount( $aCache, $sCatId, $sActIdent )
    {
        $oArticle = oxNew( 'oxarticle' );
        $sTable   = $oArticle->getViewName();
        $sO2CView = getViewName( 'oxobject2category' );

        // we use distinct if article is assigned to category twice
        $sQ  = "select count(*) from (select distinct oxobject2category.oxcatnid, oxobject2category.oxobjectid from $sO2CView as oxobject2category WHERE oxobject2category.oxcatnid = '".$sCatId."' ";
        $sQ .= "and oxobject2category.oxobjectid in ( SELECT oxid FROM $sTable where ".$oArticle->getSqlActiveSnippet().") ) as ox2cat";

        $aCache[$sCatId][$sActIdent] = oxDb::getDb()->getOne( $sQ );

        $this->_setCatCache( $aCache );
        return $aCache[$sCatId][$sActIdent];
    }

    /**
     * Saves (if needed) and returns price category article count into cache
     *
     * @param array  $aCache     Category cache data
     * @param string $sCatId     Unique category ident
     * @param string $sActIdent  Category ID
     * @param int    $dPriceFrom Price from
     * @param int    $dPriceTo   Price to
     *
     * @return null
     */
    public function setPriceCatArticleCount( $aCache, $sCatId, $sActIdent, $dPriceFrom, $dPriceTo )
    {
        $oArticle = oxNew( 'oxarticle' );
        $sTable   = $oArticle->getViewName();

        $sSubSelect  = "select if(oxparentid='',oxid,oxparentid) as id from oxarticles where oxprice >0 ";
        $sSubSelect .= $dPriceTo?"and oxprice <= $dPriceTo ":" ";
        $sSubSelect .= "group by id having ";
        $sSubSelect .= $dPriceFrom?"min(oxprice) >= $dPriceFrom ":" ";

        $sSelect  = "select count($sTable.oxid) from $sTable where ";
        $sSelect .= "$sTable.oxid in ($sSubSelect) ";
        $sSelect .= "and $sTable.oxissearch = 1 and ".$oArticle->getSqlActiveSnippet();

        $aCache[$sCatId][$sActIdent] = oxDb::getDb()->getOne( $sSelect );

        $this->_setCatCache( $aCache );
        return $aCache[$sCatId][$sActIdent];
    }

    /**
     * Saves and returns vendors category article count into cache
     *
     * @param array  $aCache    Category cache data
     * @param string $sCatId    Unique vendor category ident
     * @param string $sActIdent Vendor category ID
     *
     * @return int
     */
    public function setVendorArticleCount( $aCache, $sCatId, $sActIdent )
    {
        // if vendor/category name is 'root', skip counting
        if ( $sCatId == 'root' ) {
            return 0;
        }

        $oArticle = oxNew( 'oxarticle' );
        $sTable   = $oArticle->getViewName();

        // select each vendor articles count
        $sQ  = "select $sTable.oxvendorid AS vendorId, count(*) from $sTable where ";
        $sQ .= "$sTable.oxvendorid <> '' and ".$oArticle->getSqlActiveSnippet()." group by $sTable.oxvendorid ";
        $aDbResult = oxDb::getDb()->getAssoc( $sQ );

        foreach ( $aDbResult as $sKey => $sValue ) {
            $aCache[$sKey][$sActIdent] = $sValue;
        }

        $this->_setVendorCache( $aCache );
        return $aCache[$sCatId][$sActIdent];
    }

    /**
     * Saves and returns Manufacturers category article count into cache
     *
     * @param array  $aCache    Category cache data
     * @param string $sCatId    Unique Manufacturer category ident
     * @param string $sActIdent Manufacturer category ID
     *
     * @return int
     */
    public function setManufacturerArticleCount( $aCache, $sCatId, $sActIdent )
    {
        // if Manufacturer/category name is 'root', skip counting
        if ( $sCatId == 'root' ) {
            return 0;
        }

        $oArticle = oxNew( 'oxarticle' );
        $sTable   = $oArticle->getViewName();

        // select each Manufacturer articles count
        $sQ  = "select $sTable.oxmanufacturerid AS manufacturerId, count(*) from $sTable where ";
        $sQ .= "$sTable.oxmanufacturerid <> '' and ".$oArticle->getSqlActiveSnippet()." group by $sTable.oxmanufacturerid ";
        $aDbResult = oxDb::getDb()->getAssoc( $sQ );

        foreach ( $aDbResult as $sKey => $sValue ) {
            $aCache[$sKey][$sActIdent] = $sValue;
        }

        $this->_setManufacturerCache( $aCache );
        return $aCache[$sCatId][$sActIdent];
    }

    /**
     * Resets category (all categories) article count
     *
     * @param string $sCatId Category/vendor/manufacturer ID
     *
     * @return null
     */
    public function resetCatArticleCount( $sCatId = null )
    {
        if ( !$sCatId ) {
            $this->getConfig()->setGlobalParameter( 'aLocalCatCache', null );
        } else {   // loading from cache
            $aCatData = $this->_getCatCache();
            if ( isset( $aCatData[$sCatId] ) ) {
                unset( $aCatData[$sCatId] );
                $this->_setCatCache( $aCatData );
            }
        }

    }

    /**
     * Resets price categories article count
     *
     * @param int $iPrice article price
     *
     * @return null
     */
    public function resetPriceCatArticleCount( $iPrice )
    {
        // loading from cache
        if ( $aCatData = $this->_getCatCache() ) {

            $sTable  = getViewName( 'oxcategories' );
            $sSelect = "select $sTable.oxid from $sTable where '$iPrice' >= $sTable.oxpricefrom and '$iPrice' <= $sTable.oxpriceto ";

            $rs = oxDb::getDb()->execute( $sSelect );
            if ( $rs != false && $rs->recordCount() > 0 ) {
                while ( !$rs->EOF ) {
                    if ( isset( $aCatData[$rs->fields[0]] ) ) {
                        unset( $aCatData[$rs->fields[0]] );
                    }
                    $rs->moveNext();
                }

                // writing back to cache
                $this->_setCatCache( $aCatData );
            }

        }
    }

    /**
     * Returns specified Tag article count
     *
     * @param string $sTag  tag to search article count
     * @param int    $iLang language
     *
     * @return int
     */
    public function getTagArticleCount( $sTag, $iLang )
    {
        $sLangExt = oxLang::getInstance()->getLanguageTag( $iLang );
        $oDb = oxDb::getDb();

        $oArticle = oxNew("oxarticle");
        $sArticleTable  = $oArticle->getViewName();
        $sActiveSnippet = $oArticle->getSqlActiveSnippet();

        $sQ = "select count(*) from oxartextends inner join $sArticleTable
               on $sArticleTable.oxid = oxartextends.oxid where $sActiveSnippet
               and match(oxartextends.oxtags$sLangExt)
               against ( ".$oDb->quote( $sTag )." ) ";

        return $oDb->getOne( $sQ );
    }

    /**
     * Resets vendor (all vendors) article count
     *
     * @param string $sVendorId Category/vendor ID
     *
     * @return null
     */
    public function resetVendorArticleCount( $sVendorId = null )
    {
        if ( !$sVendorId ) {
            $this->getConfig()->setGlobalParameter( 'aLocalVendorCache', null );
            oxUtils::getInstance()->toFileCache( 'aLocalVendorCache', '' );
        } else { // loading from cache
            $aVendorData = $this->_getVendorCache();
            if ( isset( $aVendorData[$sVendorId] ) ) {
                unset( $aVendorData[$sVendorId] );
                $this->_setVendorCache( $aVendorData );
            }
        }

    }

    /**
     * Resets Manufacturer (all Manufacturers) article count
     *
     * @param string $sManufacturerId Category/Manufacturer ID
     *
     * @return null
     */
    public function resetManufacturerArticleCount( $sManufacturerId = null )
    {
        if ( !$sManufacturerId ) {
            $this->getConfig()->setGlobalParameter( 'aLocalManufacturerCache', null );
            oxUtils::getInstance()->toFileCache( 'aLocalManufacturerCache', '' );
        } else { // loading from cache
            $aManufacturerData = $this->_getManufacturerCache();
            if ( isset( $aManufacturerData[$sManufacturerId] ) ) {
                unset( $aManufacturerData[$sManufacturerId] );
                $this->_setManufacturerCache( $aManufacturerData );
            }
        }

    }

    /**
     * Loads and returns category cache data array
     *
     * @return array
     */
    protected function _getCatCache()
    {
        $myConfig = $this->getConfig();

        // first look at the local cache
        $aLocalCatCache = $myConfig->getGlobalParameter( 'aLocalCatCache' );

        // if local cache is not set - loading from file cache
        if ( !$aLocalCatCache ) {
            $sLocalCatCache = oxUtils::getInstance()->fromFileCache( 'aLocalCatCache');
            if ( $sLocalCatCache ) {
                $aLocalCatCache = unserialize( $sLocalCatCache );
            } else {
                $aLocalCatCache = null;
            }
            $myConfig->setGlobalParameter( 'aLocalCatCache', $aLocalCatCache );
        }
        return $aLocalCatCache;
    }

    /**
     * Writes category data into cache
     *
     * @param array $aCache A cacheable data
     *
     * @return null
     */
    protected function _setCatCache( $aCache )
    {
        $this->getConfig()->setGlobalParameter( 'aLocalCatCache', $aCache );
        oxUtils::getInstance()->toFileCache( 'aLocalCatCache', serialize( $aCache ) );
    }

    /**
     * Writes vendor data into cache
     *
     * @param array $aCache A cacheable data
     *
     * @return null
     */
    protected function _setVendorCache( $aCache )
    {
        $this->getConfig()->setGlobalParameter( 'aLocalVendorCache', $aCache );
        oxUtils::getInstance()->toFileCache( 'aLocalVendorCache', serialize( $aCache ) );
    }

    /**
     * Writes Manufacturer data into cache
     *
     * @param array $aCache A cacheable data
     *
     * @return null
     */
    protected function _setManufacturerCache( $aCache )
    {
        $this->getConfig()->setGlobalParameter( 'aLocalManufacturerCache', $aCache );
        oxUtils::getInstance()->toFileCache( 'aLocalManufacturerCache', serialize( $aCache ) );
    }

    /**
     * Loads and returns category/vendor cache data array
     *
     * @return array
     */
    protected function _getVendorCache()
    {
        $myConfig = $this->getConfig();

        // first look at the local cache
        $aLocalVendorCache = $myConfig->getGlobalParameter('aLocalVendorCache');
        // if local cache is not set - loading from file cache
        if ( !$aLocalVendorCache ) {
            $sLocalVendorCache = oxUtils::getInstance()->fromFileCache( 'aLocalVendorCache' );
            if ( $sLocalVendorCache ) {
                $aLocalVendorCache = unserialize( $sLocalVendorCache );
            } else {
                $aLocalVendorCache = null;
            }
            $myConfig->setGlobalParameter( 'aLocalVendorCache', $aLocalVendorCache );
        }
        return $aLocalVendorCache;
    }

    /**
     * Loads and returns category/Manufacturer cache data array
     *
     * @return array
     */
    protected function _getManufacturerCache()
    {
        $myConfig = $this->getConfig();

        // first look at the local cache
        $aLocalManufacturerCache = $myConfig->getGlobalParameter('aLocalManufacturerCache');
        // if local cache is not set - loading from file cache
        if ( !$aLocalManufacturerCache ) {
            $sLocalManufacturerCache = oxUtils::getInstance()->fromFileCache( 'aLocalManufacturerCache' );
            if ( $sLocalManufacturerCache ) {
                $aLocalManufacturerCache = unserialize( $sLocalManufacturerCache );
            } else {
                $aLocalManufacturerCache = null;
            }
            $myConfig->setGlobalParameter( 'aLocalManufacturerCache', $aLocalManufacturerCache );
        }
        return $aLocalManufacturerCache;
    }

    /**
    * Returns user view id (Shop, language, RR group index...)
    *
    * @param bool $blReset optiona, default = false
    *
    * @return string
    */
    protected function _getUserViewId( $blReset = false )
    {
        if ( $this->_sUserViewId != null && !$blReset ) {
            return $this->_sUserViewId;
        }

        // loading R&R data from session
        $aRRIdx = null;

        $this->_sUserViewId = md5($this->getConfig()->getShopID().oxLang::getInstance()->getLanguageTag().serialize($aRRIdx).(int) $this->isAdmin() );
        return $this->_sUserViewId;
    }

}
