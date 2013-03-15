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
 * $Id: basket.php 17315 2009-03-17 16:18:58Z arvydas $
 */

/**
 * Current session shopping cart (basket item list).
 * Contains with user selected articles (with detail information), list of
 * similar products, topoffer articles.
 * OXID eShop -> SHOPPING CART.
 */
class Basket extends oxUBase
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'basket.tpl';

    /**
     * Template for search engines
     *
     * @var string
     */
    protected $_sThisAltTemplate = 'content.tpl';

    /**
     * Order step marker
     *
     * @var bool
     */
    protected $_blIsOrderStep = true;

    /**
     * all basket articles
     *
     *@var object
     */
    protected $_oBasketArticles = null;

    /**
     * Similar List
     *
     *@var object
     */
    protected $_oSimilarList = null;

    /**
     * Recomm List
     *
     *@var object
     */
    protected $_oRecommList = null;

    /**
     * First basket product object. It is used to load
     * recomendation list info and similar product list
     *
     * @var oxarticle
     */
    protected $_oFirstBasketProduct = null;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Executes parent::render(), creates list with basket articles
     * Returns name of template file basket::_sThisTemplate (for Search
     * engines return "content.tpl" template to avoid fake orders etc).
     *
     * Template variables:
     * <b>similarlist</b>, <b>basketitemlist</b>
     *
     * @return  string   $this->_sThisTemplate  current template file name
     */
    public function render()
    {
        parent::render();

        // checks if current http client is SE and skips basket preview on success
        if ( oxUtils::getInstance()->isSearchEngine() ) {
            return $this->_sThisTemplate = $this->_sThisAltTemplate;
        }

        //for older templates
        $this->_aViewData['basketitemlist']    = $this->getBasketArticles();
        $this->_aViewData['basketsimilarlist'] = $this->getBasketSimilarList();
        $this->_aViewData['similarrecommlist'] = $this->getSimilarRecommLists();
        $this->_aViewData['showbacktoshop']    = $this->showBackToShop();


        return $this->_sThisTemplate;
    }

    /**
     * Return the current articles from the basket
     *
     * @return object | bool
     */
    public function getBasketArticles()
    {
        if ( $this->_oBasketArticles === null) {
            $this->_oBasketArticles = false;

            // passing basket articles
            if ( $oBasket = $this->getSession()->getBasket() ) {
                $this->_oBasketArticles = $oBasket->getBasketArticles();
            }
        }
        return $this->_oBasketArticles;
    }

    /**
     * return the basket articles
     *
     * @return object | bool
     */
    public function getFirstBasketProduct()
    {
        if ( $this->_oFirstBasketProduct === null ) {
            $this->_oFirstBasketProduct = false;

            $aBasketArticles = $this->getBasketArticles();
            if ( is_array( $aBasketArticles ) && $oProduct = reset( $aBasketArticles ) ) {
                $this->_oFirstBasketProduct = $oProduct;
            }
        }
        return $this->_oFirstBasketProduct;
    }

    /**
     * return the similar articles
     *
     * @return object | bool
     */
    public function getBasketSimilarList()
    {
        if ( $this->_oSimilarList === null) {
            $this->_oSimilarList = false;

            // similar product info
            if ( $oProduct = $this->getFirstBasketProduct() ) {
                $this->_oSimilarList = $oProduct->getSimilarProducts();
            }
        }
        return $this->_oSimilarList;
    }

    /**
     * return the recomm articles
     *
     * @return object | bool
     */
    public function getSimilarRecommLists()
    {
        if ( $this->_oRecommList === null) {
            $this->_oRecommList = false;

            if ( $oProduct = $this->getFirstBasketProduct() ) {
                $oRecommList = oxNew('oxrecommlist');
                $this->_oRecommList = $oRecommList->getRecommListsByIds( array( $oProduct->getId() ) );
            }
        }
        return $this->_oRecommList;
    }

    /**
     * return the Link back to shop
     *
     * @return bool
     */
    public function showBackToShop()
    {
        return ( $this->getConfig()->getConfigParam( 'iNewBasketItemMessage' ) == 3 && oxSession::hasVar( '_backtoshop' ) );
    }

    /**
     * Assigns voucher to current basket
     *
     * @return null
     */
    public function addVoucher()
    {
        $oBasket = $this->getSession()->getBasket();
        $oBasket->addVoucher( oxConfig::getParameter( 'voucherNr' ) );
    }

    /**
     * Removes voucher from basket (calls oxbasket::removeVoucher())
     *
     * @return null
     */
    public function removeVoucher()
    {
        $oBasket = $this->getSession()->getBasket();
        $oBasket->removeVoucher( oxConfig::getParameter( 'voucherId' ) );
    }

    /**
     * Redirects user back to previous part of shop (list, details, ...) from basket.
     * Used with option "Display Message when Product is added to Cart" set to "Open Basket"
     * ($myConfig->iNewBasketItemMessage == 3)
     *
     * @return string   $sBackLink  back link
     */
    public function backToShop()
    {
        if ( $this->getConfig()->getConfigParam( 'iNewBasketItemMessage' ) == 3 ) {
            if ( $sBackLink = oxSession::getVar( '_backtoshop' ) ) {
                oxSession::deleteVar( '_backtoshop' );
                return $sBackLink;
            }
        }
    }
}
