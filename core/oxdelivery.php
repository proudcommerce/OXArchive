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
 * $Id: oxdelivery.php 19551 2009-06-02 08:36:03Z arvydas $
 */

/**
 * Order delivery manager.
 * Currently calculates price/costs.
 * @package core
 */
class oxDelivery extends oxI18n
{
    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxdelivery';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxdelivery';

    /**
     * Total count of product items which are covered by current delivery
     * (used for caching purposes across several methods)
     *
     * @var double
     */
    protected $_iItemCnt = 0;

    /**
     * Total count of products which are covered by current delivery
     * (used for caching purposes across several methods)
     *
     * @var double
     */
    protected $_iProdCnt = 0;

    /**
     * Total price of products which are covered by current delivery
     * (used for caching purposes across several methods)
     *
     * @var double
     */
    protected $_dPrice = 0;

    /**
     * Current delivery price object which keeps price info
     *
     * @var oxprice
     */
    protected $_oPrice = null;

    /**
     * Article Ids which are assigned to current delivery
     *
     * @var array
     */
    protected $_aArtIds = null;

    /**
     * Category Ids which are assigned to current delivery
     *
     * @var array
     */
    protected $_aCatIds = null;

    /**
     * If article has free shipping
     *
     * @var bool
     */
    protected $_blFreeShipping = true;

    /**
     * Product list storage
     *
     * @var array
     */
    protected static $_aProductList = array();

    /**
     * Wrapping VAT config
     *
     * @var bool
     */
    protected $_blDelVatOnTop = false;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxdelivery' );
        $this->setDelVatOnTop( $this->getConfig()->getConfigParam( 'blDeliveryVatOnTop' ) );
    }

    /**
     * Delivery VAT config setter
     *
     * @param bool $blOnTop delivery vat config
     *
     * @return null
     */
    public function setDelVatOnTop( $blOnTop )
    {
        $this->_blDelVatOnTop = $blOnTop;
    }

    /**
     * Collects article Ids which are assigned to current delivery
     *
     * @return array
     */
    public function getArticles()
    {
        if ( $this->_aArtIds !== null ) {
            return $this->_aArtIds;
        }

        $sQ = 'select oxobjectid from oxobject2delivery where oxdeliveryid="'.$this->getId().'" and oxtype = "oxarticles" ';

        $aArtIds = oxDb::getDb()->getArray( $sQ );

        //make single dimension array
        foreach ( $aArtIds as $aItem ) {
            $this->_aArtIds[] = $aItem[0];
        }

        return $this->_aArtIds;

    }

    /**
     * Collects category Ids which are assigned to current delivery
     *
     * @return array
     */
    public function getCategories()
    {
        if ( $this->_aCatIds !== null ) {
            return $this->_aCatIds;
        }

        $sQ = 'select oxobjectid from oxobject2delivery where oxdeliveryid="'.$this->getId().'" and oxtype = "oxcategories" ';

        $aCatIds = oxDb::getDb()->getAll( $sQ );

        //make single dimension array
        foreach ( $aCatIds AS $aItem ) {
            $this->_aCatIds[] = $aItem[0];
        }

        return $this->_aCatIds;
    }

    /**
     * Checks if delivery has assigned articles
     *
     * @return bool
     */
    public function hasArtices()
    {
        return ( bool ) count( $this->getArticles() );
    }

    /**
     * Checks if delivery has assigned categories
     *
     * @return bool
     */
    public function hasCategories()
    {
        return ( bool ) count( $this->getCategories() );
    }

    /**
     * Returns amount (total net price/weight/volume/Amount) on which delivery price is applied
     *
     * @param object $oBasketItem basket item object
     *
     * @return double
     */
    public function getDeliveryAmount( $oBasketItem )
    {
        $dAmount = 0 ;

        $blExclNonMaterial = $this->getConfig()->getConfigParam( 'blExclNonMaterialFromDelivery' );
        // calculating only the price which is for non free shipping products
        if ( !$oBasketItem->getArticle()->oxarticles__oxfreeshipping->value &&
              !( $oBasketItem->getArticle()->oxarticles__oxnonmaterial->value && $blExclNonMaterial ) ) {

            $this->_blFreeShipping = false;

            switch ( $this->oxdelivery__oxdeltype->value ) {
                case 'p': // price
                    $dAmount += $oBasketItem->getPrice()->getBruttoPrice(); // price// currency conversion must allready be done in price class / $oCur->rate; // $oBasketItem->oPrice->getPrice() / $oCur->rate;
                    break;
                case 'w': // weight
                    $dAmount += $oBasketItem->getWeight();
                    break;
                case 's': // size
                    $dAmount += $oBasketItem->getArticle()->oxarticles__oxlength->value *
                                $oBasketItem->getArticle()->oxarticles__oxwidth->value *
                                $oBasketItem->getArticle()->oxarticles__oxheight->value *
                                $oBasketItem->getAmount();
                    break;
                case 'a': // amount
                    $dAmount += $oBasketItem->getAmount();
                    break;
            }

            $this->_iItemCnt += $oBasketItem->getAmount();
            $this->_iProdCnt += 1;
            $this->_dPrice   += $oBasketItem->getPrice()->getBruttoPrice();
        }

        return $dAmount;
    }

    /**
     * Delivery price setter
     *
     * @param oxprice $oPrice delivery price to set
     *
     * @return null
     */
    public function setDeliveryPrice( $oPrice )
    {
        $this->_oPrice = $oPrice;
    }

    /**
     * Returns oxprice object for delivery costs
     *
     * @param double $dVat delivery vat
     *
     * @return oxPrice
     */
    public function getDeliveryPrice( $dVat = null )
    {
        if ( $this->_oPrice === null ) {
            // loading oxprice object for final price calculation
            $this->_oPrice = oxNew( 'oxPrice' );

            if ( !$this->_blDelVatOnTop ) {
                $this->_oPrice->setBruttoPriceMode();
            } else {
                $this->_oPrice->setNettoPriceMode();
            }

            $this->_oPrice->setVat( $dVat );

            // if article is free shipping, price for delivery will be not calculated
            if ( $this->_blFreeShipping ) {
                return $this->_oPrice;
            }

            // calculating base price value
            switch ( $this->oxdelivery__oxaddsumtype->value ) {
                case 'abs':

                    $dAmount = 0;

                    if ( $this->oxdelivery__oxfixed->value == 0 ) { // 1. Once per Cart
                        $dAmount = 1;
                    } elseif ( $this->oxdelivery__oxfixed->value == 1 ) { // 2. Once per Product overall
                        $dAmount = $this->_iProdCnt;
                    } elseif ( $this->oxdelivery__oxfixed->value == 2 ) { // 3. Once per Product in Cart
                        $dAmount = $this->_iItemCnt;
                    }

                    $oCur = $this->getConfig()->getActShopCurrencyObject();
                    $this->_oPrice->add( $this->oxdelivery__oxaddsum->value * $oCur->rate );
                    $this->_oPrice->multiply( $dAmount );
                    break;
                case '%':

                    $this->_oPrice->add( $this->_dPrice /100 * $this->oxdelivery__oxaddsum->value );
                    break;
            }
        }

        // calculating total price
        return $this->_oPrice;
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $sOXID Object ID (default null)
     *
     * @return bool
     */
    public function delete( $sOXID = null )
    {
        if ( !$sOXID ) {
            $sOXID = $this->getId();
        }
        if ( !$sOXID ) {
            return false;
        }


        $sQ = "delete from oxobject2delivery where oxobject2delivery.oxdeliveryid = '".$sOXID."' ";
        oxDb::getDb()->execute( $sQ );

        return parent::delete( $sOXID );
    }

    /**
     * Checks if delivery fits for current basket
     *
     * @param oxbasket $oBasket shop basket
     *
     * @return bool
     */
    public function isForBasket( $oBasket )
    {
        // amount for conditional check
        $blHasArticles   = $this->hasArtices();
        $blHasCategories = $this->hasCategories();
        $blUse = true;
        $iAmount = 0;

        // category & article check
        if ( $blHasCategories || $blHasArticles ) {
            $blUse = false;

            $aDeliveryArticles   = $this->getArticles();
            $aDeliveryCategories = $this->getCategories();

            foreach ( $oBasket->getContents() as $oContent ) {

                //V FS#1954 - load delivery for variants from parent article
                $oArticle   = $oContent->getArticle();
                $sProductId = $oArticle->getId();
                $sParentId  = $oArticle->oxarticles__oxparentid->value ? $oArticle->oxarticles__oxparentid->value : false;

                if ( $blHasArticles && (in_array( $sProductId, $aDeliveryArticles ) || ( $sParentId && in_array( $sParentId, $aDeliveryArticles ) ) ) ) {
                    $blUse = true;
                    $iAmount += $this->getDeliveryAmount( $oContent );

                } elseif ( $blHasCategories ) {

                    if ( isset( self::$_aProductList[$sProductId] ) ) {
                        $oProduct = self::$_aProductList[$sProductId];
                    } else {
                        $oProduct = oxNew( 'oxarticle' );
                        $oProduct->setSkipAssign( true );

                        if ( !$oProduct->load( $sProductId ) ) {
                            continue;
                        }

                        $oProduct->setId($sProductId);
                        self::$_aProductList[$sProductId] = $oProduct;
                    }

                    foreach ( $aDeliveryCategories as $sCatId ) {

                        if ( $oProduct->inCategory( $sCatId ) ) {
                            $blUse = true;

                            $iAmount += $this->getDeliveryAmount( $oContent );

                            break;
                        }
                    }
                }
            }

        } else { // regular amounts check

            foreach ( $oBasket->getContents() as $oContent ) {
                $iAmount += $this->getDeliveryAmount( $oContent );
            }
        }

        $blForBasket = false;
        if ( $blUse && $this->_checkDeliveryAmount($iAmount) ) {
            $blForBasket = true;
        }
        return $blForBasket;
    }

    /**
     * checks if amount param is ok for this delivery
     *
     * @param double $iAmount amount
     *
     * @return boolean
     */
    protected function _checkDeliveryAmount($iAmount)
    {
        switch ( $this->oxdelivery__oxdeltype->value ) {
            case 'p': // price
                $oCur = $this->getConfig()->getActShopCurrencyObject();
                $iAmount /= $oCur->rate;
                break;
            case 'w': // weight
            case 's': // size
            case 'a': // amount
                break;
        }

        if ( $iAmount >= $this->oxdelivery__oxparam->value && $iAmount <= $this->oxdelivery__oxparamend->value ) {
            return true;
        }

        return false;
    }
}
