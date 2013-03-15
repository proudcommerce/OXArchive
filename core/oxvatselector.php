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
 * $Id: oxvatselector.php 20503 2009-06-26 14:54:11Z vilma $
 */

/**
 * Class, responsible for retrieving correct vat for users and articles
 *
 * @package core
 */
class oxVatSelector extends oxSuperCfg
{


    /**
     * oxVatSelector instance
     *
     * @var oxVatSelector
     */
    protected static $_instance = null;

    /**
     * Returns singleton oxVatSelector object instance or create new if needed
     *
     * @return oxVatSelector
     */
    public static function getInstance()
    {
        if ( !self::$_instance instanceof oxVatSelector ) {
                self::$_instance = oxNew('oxVatSelector');
        }
        return self::$_instance;
    }

    /**
     * keeps loaded user Vats for later reusage
     *
     * @var array
     */
    protected static $_aUserVatCache = array();

    /**
     * get VAT for user, can NOT be null
     *
     * @param oxUser $oUser        given user object
     * @param bool   $blCacheReset reset cache
     *
     * @throws oxObjectException if wrong country
     * @return double | false
     */
    public function getUserVat(oxUser $oUser, $blCacheReset = false)
    {
        if (!$blCacheReset) {
            if (self::$_aUserVatCache[$oUser->getId()] !== null) {
                return self::$_aUserVatCache[$oUser->getId()];
            }
        }

        $ret = false;

        if ($sCountryId = $oUser->oxuser__oxcountryid->value) {
            $oCountry = oxNew('oxcountry');
            if (!$oCountry->load($sCountryId)) {
                throw new oxObjectException();
            }
            if ($oCountry->isForeignCountry()) {
                $ret = $this->_getForeignCountryUserVat($oUser, $oCountry);
            }
        }

        self::$_aUserVatCache[$oUser->getId()] = $ret;
        return $ret;
    }

    /**
     * get vat for user of a foreign country
     *
     * @param oxUser    $oUser    given user object
     * @param oxCountry $oCountry given country object
     *
     * @return unknown
     */
    protected function _getForeignCountryUserVat(oxUser $oUser, oxCountry $oCountry )
    {
        if ($oCountry->isInEU()) {
            if ($oUser->oxuser__oxustid->value && $oUser->oxuser__oxcompany->value) {
                return 0;
            }
            return false;
        }

        return 0;
    }

    /**
     * return Vat value for oxcategory type assignment only
     *
     * @param oxArticle $oArticle given article
     *
     * @return float | false
     */
    protected function _getVatForArticleCategory(oxArticle $oArticle)
    {
        //return false;
        //if (count($aCats)) {
        //$sMainCat  = $aCats[0];
        //$aCats = $oArticle->getCategoryIds();

        $oDb = oxDb::getDb();
        $sCatT = getViewName('oxcategories');
        $sSelect = "SELECT oxid
                    FROM $sCatT
                    WHERE oxvat IS NOT NULL LIMIT 1";

        //no category specific vats in shop?
        //then for performance reasons we just return false
        $iCount = $oDb->getOne($sSelect);
        if (!$iCount) {
            return false;
        }

        $sO2C = getViewName('oxobject2category');
        $sSql = "SELECT c.oxvat
                 FROM $sCatT AS c, $sO2C AS o2c
                 WHERE c.oxid=o2c.oxcatnid AND
                       o2c.oxobjectid = '".$oArticle->getId()."' AND
                       c.oxvat IS NOT NULL
                 ORDER BY o2c.oxtime ";

        //echo $sSql."<br>";

        $fVat = $oDb->getOne($sSql);
        if ($fVat !== false && $fVat !== null) {
            return $fVat;
        }

        //}
        return false;
    }

    /**
     * get VAT for given article, can NOT be null
     *
     * @param oxArticle $oArticle given article
     *
     * @return double
     */
    public function getArticleVat(oxArticle $oArticle)
    {
        startProfile("_assignPriceInternal");
        // article has its own VAT ?

        if ( ( $dArticleVat = $oArticle->getCustomVAT() ) !== null ) {
            stopProfile("_assignPriceInternal");
            return $dArticleVat;
        }
        if ( ( $dArticleVat = $this->_getVatForArticleCategory($oArticle) ) !== false ) {
            stopProfile("_assignPriceInternal");
            return $dArticleVat;
        }

        stopProfile("_assignPriceInternal");
        return $this->getConfig()->getConfigParam( 'dDefaultVAT' );
    }

    /**
     * Currently returns vats percent that can be applied for basket
     * item ( executes oxVatSelector::getArticleVat()). Can be used to override
     * basket price calculation behaviour (oxarticle::getBasketPrice())
     *
     * @param object $oArticle article object
     * @param object $oBasket  oxbasket object
     *
     * @return double
     */
    public function getBasketItemVat(oxArticle $oArticle, $oBasket )
    {
        return $this->getArticleVat( $oArticle );
    }

    /**
     * get article user vat
     *
     * @param oxArticle $oArticle article object
     *
     * @return double | false
     */
    public function getArticleUserVat(oxArticle $oArticle)
    {
        if ( ( $oUser = $oArticle->getArticleUser() ) ) {
            return $this->getUserVat( $oUser );
        }
        return false;
    }

}
