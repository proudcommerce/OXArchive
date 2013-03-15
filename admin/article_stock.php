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
 * @package admin
 * @copyright (C) OXID eSales AG 2003-2009
 * @version OXID eShop CE
 * $Id: article_stock.php 17188 2009-03-13 12:19:11Z arvydas $
 */

/**
 * Admin article inventory manager.
 * Collects such information about article as stock quantity, delivery status,
 * stock message, etc; Updates information (on user submit).
 * Admin Menu: Manage Products -> Articles -> Inventory.
 * @package admin
 */
class Article_Stock extends oxAdminDetails
{
    /**
     * Loads article Inventory information, passes it to Smarty engine and
     * returns name of template file "article_stock.tpl".
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $this->_aViewData["edit"] = $oArticle = oxNew( "oxarticle");

        $soxId = oxConfig::getParameter( "oxid");
        if ( $soxId != "-1" && isset( $soxId)) {

            // load object
            $oArticle->loadInLang( $this->_iEditLang, $soxId );

            // load object in other languages
            $oOtherLang = $oArticle->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oArticle->loadInLang( key($oOtherLang), $soxId );
            }

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new oxStdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] =  clone $oLang;
            }


            // variant handling
            if ( $oArticle->oxarticles__oxparentid->value) {
                $oParentArticle = oxNew( "oxarticle");
                $oParentArticle->load( $oArticle->oxarticles__oxparentid->value);
                $this->_aViewData["parentarticle"] =  $oParentArticle;
                $this->_aViewData["oxparentid"] =  $oArticle->oxarticles__oxparentid->value;
            }

            $sShopID = $myConfig->getShopID();
            $oPriceList = oxNew("oxlist");
            $oPriceList->init( 'oxbase', "oxprice2article" );
            $sQ = "select * from oxprice2article where oxartid = '$soxId' and oxshopid = '$sShopID' and (oxamount > 0 or oxamountto > 0) order by oxamount ";
            $oPriceList->selectstring( $sQ );

            $this->_aViewData["amountprices"] = $oPriceList;

        }

        return "article_stock.tpl";
    }

    /**
     * Saves article Inventori information changes.
     *
     * @return mixed
     */
    public function save()
    {

        $soxId      = oxConfig::getParameter( "oxid");
        $aParams    = oxConfig::getParameter( "editval");

        // checkbox handling
        if ( !isset( $aParams['oxarticles__oxremindactive']))
            $aParams['oxarticles__oxremindactive'] = 0;

        /*
        */
            //if( !$myConfig->getConfigParam( 'blAllowUnevenAmounts') && isset($aParams['oxarticles__oxstock']))
            //    $aParams['oxarticles__oxstock'] = round((string)$aParams['oxarticles__oxstock']);

            // shopid
            $sShopID = oxSession::getVar( "actshop");
            $aParams['oxarticles__oxshopid'] = $sShopID;

        $oArticle = oxNew( "oxarticle");
        $oArticle->loadInLang( $this->_iEditLang, $soxId );
        //$aParams = $oArticle->ConvertNameArray2Idx( $aParams);
        $oArticle->setLanguage(0);
        $oArticle->assign( $aParams);
        //tells to article to load in different language
        $oArticle->setLanguage($this->_iEditLang);
        $oArticle = oxUtilsFile::getInstance()->processFiles( $oArticle );

        if ( $oArticle->oxarticles__oxremindactive->value &&
             $oArticle->oxarticles__oxremindamount->value <= $oArticle->oxarticles__oxstock->value )
            $oArticle->oxarticles__oxremindactive->value = 1;

        $oArticle->save();
    }


    /**
     * Saves article Mall information data changes.
     *
     * @return null
     */
    public function savemallstuff()
    {
        $soxId      = oxConfig::getParameter( "oxid");
        $aParams    = oxConfig::getParameter( "mall");
        $sShopID    = oxSession::getVar( "actshop");

        $oDB = oxDb::getDb();
        $sID = $oDB->getOne( "select oxid from oxprice2article where oxartid = '$soxId' and oxshopid = '$sShopID'");

        $oNew = oxNew( "oxbase");
        $oNew->init( "oxprice2article" );
        if ( isset( $sID) && $sID)
            $oNew->load( $sID);
        else {
            $oNew->oxprice2article__oxshopid->setValue($sShopID);
            $oNew->oxprice2article__oxartid->setValue($soxId);
        }
        $oNew->oxprice2article__oxaddabs->setValue($aParams['oxprice2article__oxaddabs']);
        $oNew->oxprice2article__oxaddperc->setValue($aParams['oxprice2article__oxaddperc']);
        $oNew->save();
    }

    /**
     * Assigns current article to selected shop
     *
     * @return null
     */
    public function assignShop()
    {
        $aAddShop = oxConfig::getParameter( "allshop");
        $soxId    = oxConfig::getParameter( "oxid");

        if (is_array($aAddShop))
            foreach ($aAddShop as $sShopID) {
                $oNew = oxNew( "oxbase");
                $oNew->init( "oxarticle2shop" );
                $oNew->oxarticle2shop__oxshopid = new oxField($sShopID);
                $oNew->oxarticle2shop__oxartid = new oxField($soxId);
                $oNew->save();
            }
    }

    /**
     * Removes current article from selected shop
     *
     * @return null
     */
    public function removeShop()
    {
        $aRemShop = oxConfig::getParameter( "assignedshop");
        $soxId    = oxConfig::getParameter( "oxid");

        if (is_array($aRemShop))
            foreach ( $aRemShop as $sArtId) {
                $oNew = oxNew( "oxbase" );
                $oNew->init( "oxarticle2shop" );
                $oNew->delete($sArtId);
            }
    }

    /**
     * Adds amount price to article
     *
     * @return null
     */
    public function addprice()
    {
        $myConfig = $this->getConfig();


        $soxId   = oxConfig::getParameter( "oxid");
        $aParams = oxConfig::getParameter( "editval");

        //replacing commas
        foreach ($aParams as $key => $sParam)
            $aParams[$key] = str_replace(",", ".", $sParam);

            $sShopID = $myConfig->getShopID();
            $aParams['oxprice2article__oxshopid'] = $sShopID;

        $aParams['oxprice2article__oxartid'] = $soxId;
        if (!isset($aParams['oxprice2article__oxamount']) || !$aParams['oxprice2article__oxamount'])
            $aParams['oxprice2article__oxamount'] = "1";

        if ( !$myConfig->getConfigParam( 'blAllowUnevenAmounts' ) ) {
            $aParams['oxprice2article__oxamount']   = round((string) $aParams['oxprice2article__oxamount']);
            $aParams['oxprice2article__oxamountto'] = round((string) $aParams['oxprice2article__oxamountto']);
        }
        $dPrice = $aParams['price'];
        $sType = $aParams['pricetype'];

        $oArticlePrice = oxNew( "oxbase" );
        $oArticlePrice->init( "oxprice2article" );
        $oArticlePrice->assign($aParams);

        $oArticlePrice->$sType = new oxField($dPrice);

        //validating

        if ($oArticlePrice->$sType->value &&
            $oArticlePrice->oxprice2article__oxamount->value &&
            $oArticlePrice->oxprice2article__oxamountto->value &&
            is_numeric($oArticlePrice->$sType->value) &&
            is_numeric($oArticlePrice->oxprice2article__oxamount->value) &&
            is_numeric($oArticlePrice->oxprice2article__oxamountto->value) &&
            $oArticlePrice->oxprice2article__oxamount->value <= $oArticlePrice->oxprice2article__oxamountto->value
            ) {
                $oArticlePrice->save();
        }

    }

    /**
     * Adds amount price to article
     *
     * @return null
     */
    public function deleteprice()
    {

        $sPriceID = oxConfig::getParameter("priceid");
        $sOXID = oxConfig::getParameter("oxid");
        $sQ = "delete from oxprice2article where oxid = '$sPriceID' and oxartid = '$sOXID'";
        oxDb::getDb()->Execute($sQ);
    }

}
