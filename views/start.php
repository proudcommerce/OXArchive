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
 * $Id: start.php 20551 2009-06-30 12:04:07Z arvydas $
 */

/**
 * Starting shop page.
 * Shop starter, manages starting visible articles, etc.
 */
class Start extends oxUBase
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'start.tpl';

    /**
     * Start page meta description CMS ident
     *
     * @var string
     */
    protected $_sMetaDescriptionIdent = 'oxstartmetadescription';

    /**
     * Start page meta keywords CMS ident
     *
     * @var string
     */
    protected $_sMetaKeywordsIdent = 'oxstartmetakeywords';

    /**
     * Are actions on
     *
     * @var bool
     */
    protected $_blLoadActions = null;

    /**
     * Top article list (OXTOPSTART)
     *
     * @var array
     */
    protected $_aTopArticleList = null;

    /**
     * Newest article list
     *
     * @var array
     */
    protected $_aNewArticleList = null;

    /**
     * First article (OXFIRSTSTART)
     *
     * @var object
     */
    protected $_oFirstArticle = null;

    /**
     * Category offer article (OXCATOFFER)
     *
     * @var object
     */
    protected $_oCatOfferArticle = null;

    /**
     * Category offer article list (OXCATOFFER)
     *
     * @var array
     */
    protected $_oCatOfferArtList = null;

    /**
     * Tag cloud
     *
     * @var array
     */
    protected $_sTagCloud = null;

    /**
     * Sign if to load and show top5articles action
     * @var bool
     */
    protected $_blTop5Action = true;

    /**
     * Sign if to load and show bargain action
     * @var bool
     */
    protected $_blBargainAction = true;

    /**
     * Unsets SEO category and call parent::init();
     *
     * @return null
     */
    public function init()
    {
        if ( oxUtils::getInstance()->seoIsActive() ) {
            // cleaning category id tracked by SEO
            $this->setSessionCategoryId( null );
        }

        parent::init();
    }

    /**
     * Executes parent::render(), loads action articles
     * (oxarticlelist::loadAktionArticles()). Returns name of
     * template file to render.
     *
     * Template variables:
     * <b>articlelist</b>, <b>firstarticle</b>, <b>toparticlelist</b>,
     * <b>articlebargainlist</b>
     *
     * @return  string  cuurent template file name
     */
    public function render()
    {
        if ( oxConfig::getParameter( 'showexceptionpage' ) == '1' ) {
            return 'exception.tpl';
        }

        startProfile("allarticles");

        $myConfig = $this->getConfig();
        if ( $myConfig->getConfigParam( 'bl_perfLoadAktion' ) ) {
            // loading actions
            $this->_loadActions();
            $myUtilsCount = oxUtilsCount::getInstance();

            if ( $oArtList = $this->getCatOfferArticleList() ) {
                foreach ( $oArtList as $oCatArticle ) {
                    $oCatArticle->oCategory = $oCatArticle->getCategory();
                    if ( $oCatArticle->oCategory && $myConfig->getConfigParam( 'bl_perfShowActionCatArticleCnt' ) ) {
                        $oCatArticle->oCategory->iArtCnt = $oCatArticle->oCategory->getNrOfArticles();
                        if ( $oCatArticle->oCategory->iArtCnt == -1 ) {
                            $oCatArticle->oCategory->iArtCnt = $myUtilsCount->getCatArticleCount( $oCatArticle->oCategory->oxcategories__oxid->value );
                        }
                    }
                }
            }
        }

        $this->_aViewData['articlelist']     = $this->getArticleList();
        $this->_aViewData['toparticlelist']  = $this->getTopArticleList();
        $this->_aViewData['aNewestArticles'] = $this->getNewestArticles();
        $this->_aViewData['firstarticle']    = $this->getFirstArticle();

        $this->_aViewData['articlecatofferlist'] = $this->getCatOfferArticleList();
        $this->_aViewData['articlecatoffer']     = $this->getCatOfferArticle();

        stopProfile("allarticles");

        startProfile("tagCloud");
        $this->_aViewData['tagCloud']   = $this->getTagCloud();
        $this->_aViewData['blMoreTags'] = $this->isMoreTagsVisible();
        stopProfile("tagCloud");

        $aRssSelected = $myConfig->getConfigParam( 'aRssSelected' );
        $oRss = oxNew('oxrssfeed');
        if ( is_array( $aRssSelected ) ) {
            if ($myConfig->getConfigParam( 'iTop5Mode' ) && in_array( 'oxrss_topshop', $aRssSelected ) ) {
                $this->addRssFeed( $oRss->getTopInShopTitle(), $oRss->getTopInShopUrl(), 'topArticles' );
            }
            if ( $myConfig->getConfigParam( 'iNewestArticlesMode' ) && in_array( 'oxrss_newest', $aRssSelected ) ) {
                $this->addRssFeed( $oRss->getNewestArticlesTitle(), $oRss->getNewestArticlesUrl(), 'newestArticles' );
            }
            if ( in_array( 'oxrss_bargain', $aRssSelected ) ) {
                $this->addRssFeed( $oRss->getBargainTitle(), $oRss->getBargainUrl(), 'bargainArticles' );
            }
        }

        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     * Returns current view meta data
     * If $sMeta parameter comes empty, sets to it article title and description.
     * It happens if current view has no meta data defined in oxcontent table
     *
     * @param string $sMeta     category path
     * @param int    $iLength   max length of result, -1 for no truncation
     * @param bool   $blDescTag if true - performs additional dublicate cleaning
     *
     * @return string
     */
    protected function _prepareMetaDescription( $sMeta, $iLength = 1024, $blDescTag = false )
    {
        if ( !$sMeta &&
            $this->getConfig()->getConfigParam( 'bl_perfLoadAktion' ) &&
            $oArt = $this->getFirstArticle() ) {
            $oDescField = $oArt->getArticleLongDesc();
            $sMeta = $oArt->oxarticles__oxtitle->value . ' - ' . $oDescField->value;
        }
        return parent::_prepareMetaDescription( $sMeta, $iLength, $blDescTag );
    }

    /**
     * Returns current view keywords seperated by comma
     * If $sKeywords parameter comes empty, sets to it article title and description.
     * It happens if current view has no meta data defined in oxcontent table
     *
     * @param string $sKeywords               data to use as keywords
     * @param bool   $blRemoveDuplicatedWords remove dublicated words
     *
     * @return string
     */
    protected function _prepareMetaKeyword( $sKeywords, $blRemoveDuplicatedWords = true )
    {
        if ( !$sKeywords &&
            $this->getConfig()->getConfigParam( 'bl_perfLoadAktion' ) &&
            $oArt = $this->getFirstArticle() ) {
            $oDescField = $oArt->getArticleLongDesc();
            $sKeywords = $oDescField->value;
        }

        return parent::_prepareMetaKeyword( $sKeywords, $blRemoveDuplicatedWords );
    }

    /**
     * Template variable getter. Returns if actions are ON
     *
     * @return string
     */
    protected function _getLoadActionsParam()
    {
        if ( $this->_blLoadActions === null ) {
            $this->_blLoadActions = false;
            if ( $this->getConfig()->getConfigParam( 'bl_perfLoadAktion' ) ) {
                $this->_blLoadActions = true;
            }
        }
        return $this->_blLoadActions;
    }

    /**
     * Template variable getter. Returns start page articles (OXSTART)
     *
     * @return array
     */
    public function getArticleList()
    {
        if ( $this->_aArticleList === null ) {
            $this->_aArticleList = array();
            if ( $this->_getLoadActionsParam() ) {
                // start list
                $oArtList = oxNew( 'oxarticlelist' );
                $oArtList->loadAktionArticles( 'OXSTART' );
                if ( $oArtList->count() ) {
                    $this->_aArticleList = $oArtList;
                }
            }
        }
        return $this->_aArticleList;
    }

    /**
     * Template variable getter. Returns Top article list (OXTOPSTART)
     *
     * @return array
     */
    public function getTopArticleList()
    {
        if ( $this->_aTopArticleList === null ) {
            $this->_aTopArticleList = false;
            if ( $this->_getLoadActionsParam() ) {
                // start list
                $oArtList = oxNew( 'oxarticlelist' );
                $oArtList->loadAktionArticles( 'OXTOPSTART' );
                if ( $oArtList->count() ) {
                    $this->_aTopArticleList = $oArtList;
                }
            }
        }
        return $this->_aTopArticleList;
    }

    /**
     * Template variable getter. Returns newest article list
     *
     * @return array
     */
    public function getNewestArticles()
    {
        if ( $this->_aNewArticleList === null ) {
            $this->_aNewArticleList = array();
            if ( $this->_getLoadActionsParam() ) {
                // newest articles
                $oArtList = oxNew( 'oxarticlelist' );
                $oArtList->loadNewestArticles();
                if ( $oArtList->count() ) {
                    $this->_aNewArticleList = $oArtList;
                }
            }
        }
        return $this->_aNewArticleList;
    }

    /**
     * Template variable getter. Returns first article
     *
     * @return object
     */
    public function getFirstArticle()
    {
        if ( $this->_oFirstArticle === null ) {
            $this->_oFirstArticle = false;
            if ( $this->_getLoadActionsParam() ) {
                // top articles ( big one )
                $oArtList = oxNew( 'oxarticlelist' );
                $oArtList->loadAktionArticles( 'OXFIRSTSTART' );
                if ( $oArtList->count() ) {
                    $this->_oFirstArticle = $oArtList->current();
                }
            }
        }
        return $this->_oFirstArticle;
    }

    /**
     * Template variable getter. Returns category offer article (OXCATOFFER)
     *
     * @return object
     */
    public function getCatOfferArticle()
    {
        if ( $this->_oCatOfferArticle === null ) {
            $this->_oCatOfferArticle = false;
            if ( $oArtList = $this->getCatOfferArticleList() ) {
                $this->_oCatOfferArticle = $oArtList->current();
            }
        }
        return $this->_oCatOfferArticle;
    }

    /**
     * Template variable getter. Returns category offer article list (OXCATOFFER)
     *
     * @return array
     */
    public function getCatOfferArticleList()
    {
        if ( $this->_oCatOfferArtList === null ) {
            $this->_oCatOfferArtList = array();
            if ( $this->_getLoadActionsParam() ) {
                // "category offer" articles
                $oArtList = oxNew( 'oxarticlelist' );
                $oArtList->loadAktionArticles( 'OXCATOFFER' );
                if ( $oArtList->count() ) {
                    $this->_oCatOfferArtList = $oArtList;
                }
            }
        }
        return $this->_oCatOfferArtList;
    }

    /**
     * Template variable getter. Returns tag cloud
     *
     * @return array
     */
    public function getTagCloud()
    {
        if ( $this->_sTagCloud === null ) {
            $this->_sTagCloud = false;
            $oTagHandler = oxNew('oxTagCloud');
            $this->_sTagCloud = $oTagHandler->getTagCloud();
        }
        return $this->_sTagCloud;
    }

    /**
     * Template variable getter. Returns true
     *
     * @return bool
     */
    public function isMoreTagsVisible()
    {
        return true;
    }

    /**
     * Returns SEO suffix for page title
     *
     * @return string
     */
    public function getTitleSuffix()
    {
        return $this->getConfig()->getActiveShop()->oxshops__oxstarttitle->value;
    }

}