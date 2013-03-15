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
 * @copyright � OXID eSales AG 2003-2008
 * $Id: article_review.php 14018 2008-11-06 13:33:39Z arvydas $
 */

/**
 * Admin article review manager.
 * Collects customer review about article data. There ir possibility to update
 * review text or delete it.
 * Admin Menu: Manage Products -> Articles -> Review.
 * @package admin
 */
class Article_Review extends oxAdminDetails
{
    /**
     * Loads selected article review information, returns name of template
     * file "article_review.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $this->_aViewData["edit"] = $oArticle = oxNew( "oxarticle" );

        $soxId    = oxConfig::getParameter( 'oxid' );
        $sRevoxId = oxConfig::getParameter( 'rev_oxid' );
        if ( $soxId != "-1" && isset( $soxId)) {

            // load object
            $oArticle->load( $soxId);



            $sSelect  = "select oxreviews.* from oxreviews
                         where oxreviews.OXOBJECTID = '".$oArticle->oxarticles__oxid->value
                        ."' and oxreviews.oxtype = 'oxarticle'";

            $this->aVariantList = $oArticle->getVariants();
            if ( $myConfig->getConfigParam( 'blShowVariantReviews' ) && count( $this->aVariantList )) {

                // verifying rights
                foreach ( $this->aVariantList as $oVariant ) {
                    $sSelect .= "or oxreviews.oxparentid = '".$oVariant->oxarticles__oxid->value."' ";
                }
            }

            //$sSelect .= "and oxreviews.oxtext".oxLang::getInstance()->getLanguageTag($this->_iEditLang)." != ''";
            $sSelect .= "and oxreviews.oxlang = '" . $this->_iEditLang . "'";
            $sSelect .= "and oxreviews.oxtext != '' ";

            // all reviews
            $oRevs = oxNew( "oxlist" );
            $oRevs->init( "oxreview" );
            $oRevs->selectString( $sSelect );

            foreach ( $oRevs as $oRev ) {
                if ( $oRev->oxreviews__oxid->value == $sRevoxId ) {
                    $oRev->selected = 1;
                    break;
                }
            }
            $this->_aViewData["allreviews"]   = $oRevs;
            $this->_aViewData["editlanguage"] = $this->_iEditLang;

            if ( isset( $sRevoxId ) ) {
                $oReview = oxNew( "oxreview" );
                $oReview->load( $sRevoxId );
                $this->_aViewData["editreview"] = $oReview;

                $oUser = oxNew( "oxuser" );
                $oUser->load( $oReview->oxreviews__oxuserid->value);
                $this->_aViewData["user"] = $oUser;
            }
            //show "active" checkbox if moderating is active
            $this->_aViewData["blShowActBox"] = $myConfig->getConfigParam( 'blGBModerate' );

        }

        return "article_review.tpl";
    }

    /**
     * Saves article review information changes.
     *
     * @return mixed
     */
    public function save()
    {
        $myConfig  = $this->getConfig();


        $aParams = oxConfig::getParameter( "editval");
        // checkbox handling
        if ( $myConfig->getConfigParam( 'blGBModerate' ) && !isset( $aParams['oxreviews__oxactive'] ) ) {
            $aParams['oxreviews__oxactive'] = 0;
        }

        $sRevoxId = oxConfig::getParameter( "rev_oxid" );
        $oReview  = oxNew( "oxreview" );
        $oReview->load( $sRevoxId );

            // shopid
            $oReview->oxreviews__oxshopid->value = oxSession::getVar( "actshop");

        $oReview->assign( $aParams);
        $oReview->save();

        return $this->autosave();
    }

    /**
     * Deletes selected article review information.
     *
     * @return null
     */
    public function delete()
    {

        $sRevoxId = oxConfig::getParameter( "rev_oxid" );
        $oReview  = oxNew( "oxreview" );
        $oReview->load( $sRevoxId);
        $oReview->delete();
    }
}
