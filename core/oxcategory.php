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
 * $Id: oxcategory.php 20457 2009-06-25 13:21:33Z vilma $
 */

/**
 * Category manager.
 * Collects category information (articles, etc.), performs insertion/deletion
 * of categories nodes. By recursion methods are set structure of category.
 * @package core
 */
class oxCategory extends oxI18n
{
    /**
     * Subcategories array.
     * @var array
     */
    protected $_aSubCats = array();

    /**
     * Content category array.
     * @var array
     */
    protected $_aContentCats = array();

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxcategory';

    /**
     * number of artiucles in the current category
     *
     * @var int
     */
    protected $_iNrOfArticles;

    /**
     * visibility of a category
     *
     * @var int
     */
    protected $_blIsVisible;

    /**
     * expanded state of a category
     *
     * @var int
     */
    protected $_blExpanded;

    /**
     * visibility of a category
     *
     * @var int
     */
    protected $_blHasSubCats;

    /**
     * has visible subcats state of a category
     *
     * @var int
     */
    protected $_blHasVisibleSubCats;

    /**
     * Marks that current object is managed by SEO
     *
     * @var bool
     */
    protected $_blIsSeoObject = true;

    /**
     * Set $_blUseLazyLoading to true if you want to load only actually used fields not full objet, depending on views.
     *
     * @var bool
     */
    protected $_blUseLazyLoading = false;

    /**
     * Dyn image dir
     *
     * @var string
     */
    protected $_sDynImageDir = null;

    /**
     * Top category marker
     *
     * @var bool
     */
    protected $_blTopCategory = null;

    /**
     * Category id's for sorting
     *
     * @var array
     */
    protected $_aIds = array();

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxcategories' );
    }

    /**
     * Extra getter to guarantee compatibility with templates
     *
     * @param string $sName name of variable to get
     *
     * @return unknown
     */
    public function __get( $sName )
    {
        switch ( $sName ) {
            case 'aSubCats':
                return $this->_aSubCats;
                break;
            case 'aContent':
                return $this->_aContentCats;
                break;
            case 'iArtCnt':
                return $this->getNrOfArticles();
                break;
            case 'isVisible':
                return $this->getIsVisible();
                break;
            case 'expanded':
                return $this->getExpanded();
                break;
            case 'hasSubCats':
                return $this->getHasSubCats();
                break;
            case 'hasVisibleSubCats':
                return $this->getHasVisibleSubCats();
                break;
            case 'openlink':
            case 'closelink':
            case 'link':
                //case 'toListLink':
                //case 'noparamlink':
                return $this->getLink();
                break;
            case 'dimagedir':
                return $this->getPictureUrl();
                break;
        }
        return parent::__get($sName);
    }

    /**
     * Loads and assigns object data from DB.
     *
     * @param mixed $dbRecord database record array
     *
     * @return null
     */
    public function assign( $dbRecord )
    {

        parent::assign( $dbRecord );

        startProfile("parseThroughSmarty");
        // #1030C run through smarty
        $myConfig = $this->getConfig();
        if (!$this->_isInList() && !$this->isAdmin() && $myConfig->getConfigParam( 'bl_perfParseLongDescinSmarty' ) ) {
            $this->oxcategories__oxlongdesc = new oxField( oxUtilsView::getInstance()->parseThroughSmarty( $this->oxcategories__oxlongdesc->value, $this->getId() ), oxField::T_RAW );
        }

        if ( !$this->isAdmin() && ( $myConfig->getConfigParam( 'bl_perfShowActionCatArticleCnt' ) || $myConfig->getConfigParam('blDontShowEmptyCategories')  ) ) {
            if ( $this->isPriceCategory() ) {
                $this->_iNrOfArticles = oxUtilsCount::getInstance()->getPriceCatArticleCount( $this->getId(), $this->oxcategories__oxpricefrom->value, $this->oxcategories__oxpriceto->value );
            } else {
                $this->_iNrOfArticles = oxUtilsCount::getInstance()->getCatArticleCount( $this->getId() );
            }
        }

        stopProfile("parseThroughSmarty");
    }

    /**
     * Delete empty categories, returns true on success.
     *
     * @param string $sOXID Object ID
     *
     * @return bool
     */
    public function delete( $sOXID = null)
    {
        if ( !$this->getId() ) {
            $this->load( $sOXID);
        }


        $myConfig = $this->getConfig();
        $oDB      = oxDb::getDb();
        $blRet    = false;

        if ( $this->oxcategories__oxright->value == ($this->oxcategories__oxleft->value+1) ) {
            $myUtilsPic = oxUtilsPic::getInstance();

            // only delete empty categories
            // #1173M - not all pic are deleted, after article is removed
            $myUtilsPic->safePictureDelete($this->oxcategories__oxthumb->value, $myConfig->getAbsDynImageDir().'/0', 'oxcategories', 'oxthumb' );

            $myUtilsPic->safePictureDelete($this->oxcategories__oxicon->value, $myConfig->getAbsDynImageDir().'/icon', 'oxcategories', 'oxicon' );

            $sAdd = " and oxshopid = '" . $this->getShopId() . "' ";

            $oDB->execute( "UPDATE oxcategories SET OXLEFT = OXLEFT - 2
                            WHERE  OXROOTID = '".$this->oxcategories__oxrootid->value."'
                            AND OXLEFT >   ".$this->oxcategories__oxleft->value.$sAdd );

            $oDB->execute( "UPDATE oxcategories SET OXRIGHT = OXRIGHT - 2
                            WHERE  OXROOTID = '".$this->oxcategories__oxrootid->value."'
                            AND OXRIGHT >   ".$this->oxcategories__oxright->value.$sAdd );

            // delete entry
            $blRet = parent::delete();

            // delete links to articles
            $oDB->execute( "delete from oxobject2category where oxobject2category.oxcatnid='".$this->oxcategories__oxid->value."' ");

            // #657 ADDITIONAL delete links to attributes
            $oDB->execute( "delete from oxcategory2attribute where oxcategory2attribute.oxobjectid='".$this->oxcategories__oxid->value."' ");

            // A. removing assigned:
            // - deliveries
            $oDB->execute( "delete from oxobject2delivery where oxobject2delivery.oxobjectid='".$this->oxcategories__oxid->value."' ");
            // - discounts
            $oDB->execute( "delete from oxobject2discount where oxobject2discount.oxobjectid='".$this->oxcategories__oxid->value."' ");

            oxSeoEncoderCategory::getInstance()->onDeleteCategory($this);
        }
        return $blRet;
    }

    /**
     * returns the sub category array
     *
     * @return array
     */
    public function getSubCats()
    {
        return $this->_aSubCats;
    }

    /**
     * returns a specific sub categpory
     *
     * @param string $sKey the key of the category
     *
     * @return object
     */
    public function getSubCat($sKey)
    {
        return $this->_aSubCats[$sKey];
    }

    /**
     * Sets an array of sub cetegories, handles sorting and parent hasVisibleSubCats
     *
     * @param array $aCats array of categories
     *
     * @return null
     */
    public function setSubCats( $aCats )
    {
        $this->_aSubCats = $aCats;

        foreach ( $aCats as $oCat ) {
            if ( $oCat->getIsVisible() ) {
                $this->setHasVisibleSubCats( true );
            }
        }

        $this->sortSubCats();
    }

    /**
     * sets a single category, handles sorting and parent hasVisibleSubCats
     *
     * @param oxcategory $oCat the category
     * @param string     $sKey optional, the key for that category, without a key, the category isjust added to the array
     *
     * @return null
     */
    public function setSubCat($oCat, $sKey=null)
    {
        if ( $sKey ) {
            $this->_aSubCats[$sKey] = $oCat;
        } else {
            $this->_aSubCats[] = $oCat;
        }

        // keeping ref. to parent
        $oCat->setParentCategory( $this );

        if ( $oCat->getIsVisible() ) {
            $this->setHasVisibleSubCats( true );
        }

        $this->sortSubCats();
    }

    /**
     * Sorts sub categories
     *
     * @return null
     */
    public function sortSubCats()
    {
        if ( count( $this->_aIds ) > 0 ) {
            uasort($this->_aSubCats, array( $this, 'cmpCat' ) );
        }
    }

    /**
     * categry sorting callback function, $_aIds array will be set with function
     * oxcategory::setSortingIds() and generated in oxcategorylist::sortCats()
     *
     * @param oxcategory $a first  category
     * @param oxcategory $b second category
     *
     * @return integer
     */
    public function cmpCat( $a,$b )
    {
        if ( count( $this->_aIds ) == 0 ) {
            return;
        }

        $sNumA = $this->_aIds[$a->oxcategories__oxid->value];
        $sNumB = $this->_aIds[$b->oxcategories__oxid->value];

        if ($sNumA  < $sNumB ) {
            return -1;
        } if ( $sNumA == $sNumB) {
            return 0;
        }
        return 1;
    }

    /**
     * categry sorted array
     *
     * @param array $aSorIds sorted category array
     *
     * @return null
     */
    public function setSortingIds( $aSorIds )
    {
        $this->_aIds = $aSorIds;
    }

    /**
     * returns the content category array
     *
     * @return array
     */
    public function getContentCats()
    {
        return $this->_aContentCats;
    }

    /**
     * Sets an array of content cetegories
     *
     * @param array $aContent array of content
     *
     * @return null
     */
    public function setContentCats( $aContent )
    {
        $this->_aContentCats = $aContent;
    }

    /**
     * sets a single category
     *
     * @param oxcategory $oContent the category
     * @param string     $sKey     optional, the key for that category, without a key, the category isjust added to the array
     *
     * @return null
     */
    public function setContentCat( $oContent, $sKey=null )
    {
        if ( $sKey ) {
            $this->_aContentCats[$sKey] = $oContent;
        } else {
            $this->_aContentCats[] = $oContent;
        }
    }

    /**
     * returns number or articles in category
     *
     * @return integer
     */
    public function getNrOfArticles()
    {
        if ( !$this->getConfig()->getConfigParam( 'bl_perfShowActionCatArticleCnt' ) && $this->getConfig()->getConfigParam( 'blDontShowEmptyCategories' ) ) {
            return 0;
        }

        return $this->_iNrOfArticles;
    }

    /**
     * sets the number or articles in category
     *
     * @param int $iNum category product count setter
     *
     * @return null
     */
    public function setNrOfArticles( $iNum )
    {
        $this->_iNrOfArticles = $iNum;
    }

    /**
     * returns the visibility of a category, handles hidden and empty categories
     *
     * @return bool
     */
    public function getIsVisible()
    {
        if (!isset( $this->_blIsVisible ) ) {

            if ( $this->getConfig()->getConfigParam( 'blDontShowEmptyCategories' ) ) {
                $blEmpty = ($this->_iNrOfArticles < 1) && !$this->getHasVisibleSubCats();
            } else {
                $blEmpty = false;
            }

            $this->_blIsVisible = !($blEmpty || $this->oxcategories__oxhidden->value);
        }

        return $this->_blIsVisible;
    }

    /**
     * sets the visibilty of a category
     *
     * @param bool $blVisible category visibility status setter
     *
     * @return null
     */
    public function setIsVisible( $blVisible )
    {
        $this->_blIsVisible = $blVisible;
    }

    /**
     * Returns dyn image dir
     *
     * @return string
     */
    public function getPictureUrl()
    {
        if ( $this->_sDynImageDir === null ) {
            $sThisShop = $this->oxcategories__oxshopid->value;
            $this->_sDynImageDir = $this->getConfig()->getPictureUrl( null, false, null, null, $sThisShop);
        }
        return $this->_sDynImageDir;
    }

    /**
     * returns the url of the category
     *
     * @param int $iLang language
     *
     * @return string
     */
    public function getLink($iLang = null)
    {
        if (isset($iLang)) {
            $iLang = (int) $iLang;
            if ($iLang == (int) $this->getLanguage()) {
                $iLang = null;
            }
        }
        if ( ($this->link === null ) || isset($iLang)) {
            if ( (!isset( $this->oxcategories__oxextlink->value ) || !$this->oxcategories__oxextlink->value ) &&
                 oxUtils::getInstance()->seoIsActive() ) {
                $link = oxSeoEncoderCategory::getInstance()->getCategoryUrl( $this, $iLang );
            } else {
                $link = $this->getStdLink($iLang);
            }

            if (isset($iLang)) {
                return $link;
            } else {
                $this->link = $link;
            }
        }

        return $this->link;
    }

    /**
     * sets the url of the category
     *
     * @param string $sLink category url
     *
     * @return null
     */
    public function setLink( $sLink )
    {
        $this->link = $sLink;
    }

    /**
     * Returns SQL select string with checks if items are available
     *
     * @param bool $blForceCoreTable forces core table usage (optional)
     *
     * @return string
     */
    public function getSqlActiveSnippet( $blForceCoreTable = false )
    {
            $sTable = $this->getCoreTableName();

        $sQ  = parent::getSqlActiveSnippet( $blForceCoreTable );
        $sQ .= ( strlen( $sQ )? ' and ' : '' ) . " $sTable.oxhidden = '0' ";


        return "( $sQ ) ";
    }

    /**
     * Returns standard URL to category
     *
     * @param int $iLang language
     *
     * @return string
     */
    public function getStdLink($iLang = null)
    {
        $sLink = '';
        if ( $this->oxcategories__oxextlink->value ) {
            return $this->getSession()->url( $this->oxcategories__oxextlink->value );
        } else {
            $sLink = $this->getConfig()->getShopHomeURL(). "cl=alist&amp;cnid=" . $this->getId();
        }

        if ( isset($iLang) && !oxUtils::getInstance()->seoIsActive() ) {
            $iLang = (int) $iLang;
            if ($iLang != (int) $this->getLanguage()) {
                $sLink .= "&amp;lang={$iLang}";
            }
        }

        return $sLink;
    }

    /**
     * returns the expanded state of the category
     *
     * @return bool
     */
    public function getExpanded()
    {
        if ( !isset( $this->_blExpanded ) ) {
            $myConfig = $this->getConfig();
            $this->_blExpanded = ( $myConfig->getConfigParam( 'blLoadFullTree' ) && !$myConfig->getConfigParam( 'blTopNaviLayout' ) );
        }

        return $this->_blExpanded;
    }

    /**
     * setsthe expanded state of the category
     *
     * @param bool $blExpanded expanded status setter
     *
     * @return null
     */
    public function setExpanded( $blExpanded )
    {
        $this->_blExpanded = $blExpanded;
    }

    /**
     * returns if a category has sub categories
     *
     * @return bool
     */
    public function getHasSubCats()
    {
        if ( !isset( $this->_blHasSubCats ) ) {
            $this->_blHasSubCats = $this->oxcategories__oxright->value > $this->oxcategories__oxleft->value + 1 ;
        }

        return $this->_blHasSubCats;
    }

    /**
     * returns if a category has visible sub categories
     *
     * @return bool
     */
    public function getHasVisibleSubCats()
    {
        if ( !isset( $this->_blHasVisibleSubCats ) ) {
            $this->_blHasVisibleSubCats = false;
        }

        return $this->_blHasVisibleSubCats;
    }

    /**
     * sets the state of has visible sub categories for the category
     *
     * @param bool $blHasVisibleSubcats marker if category has visible subcategories
     *
     * @return null
     */
    public function setHasVisibleSubCats( $blHasVisibleSubcats )
    {
        $this->_blHasVisibleSubCats = $blHasVisibleSubcats;
    }

    /**
     * Loads and returns attribute list associated with this category
     *
     * @return array
     */
    public function getAttributes()
    {
        $sActCat        = $this->sOXID;
        $aAttributes    = array();
        $blActiveFilter = false;

        $aSessionFilter = oxSession::getVar( 'session_attrfilter' );

        $oArtList = oxNew( "oxarticlelist");
        $oArtList->loadCategoryIDs( $sActCat, $aSessionFilter );

        // Only if we have articles
        if (count($oArtList) > 0 ) {
            $sArtIds = implode("','", array_keys($oArtList->getArray()) );
            $sAttTbl = getViewName('oxattribute');
            $sO2ATbl = getViewName('oxobject2attribute');
            $sC2ATbl = getViewName('oxcategory2attribute');
            $sLngSuf = oxLang::getInstance()->getLanguageTag($this->getLanguage());

            $sSelect = "SELECT DISTINCT att.oxid, att.oxtitle{$sLngSuf}, o2a.oxvalue{$sLngSuf} ".
                       "FROM $sAttTbl as att, $sO2ATbl as o2a ,$sC2ATbl as c2a ".
                       "WHERE att.oxid = o2a.oxattrid AND c2a.oxobjectid = '{$sActCat}' AND c2a.oxattrid = att.oxid AND o2a.oxvalue{$sLngSuf} !='' AND o2a.oxobjectid IN ('$sArtIds') ".
                       "ORDER BY c2a.oxsort , att.oxpos, att.oxtitle{$sLngSuf}, o2a.oxvalue{$sLngSuf}";

            $rs = oxDb::getDb()->Execute( $sSelect);
            if ($rs != false && $rs->recordCount() > 0) {
                $oStr = getStr();
                while ( !$rs->EOF && list($sAttId,$sAttTitle, $sAttValue) = $rs->fields ) {
                    if ( !isset( $aAttributes[$sAttId])) {
                        $oAttribute           = new stdClass();
                        $oAttribute->title    = $sAttTitle;
                        $oAttribute->aValues  = array();
                        $aAttributes[$sAttId] = $oAttribute;
                    }
                    $oValue             = new stdClass();
                    $oValue->id         = $oStr->htmlspecialchars( $sAttValue );
                    $oValue->value      = $oStr->htmlspecialchars( $sAttValue );
                    $oValue->blSelected = isset($aSessionFilter[$sActCat][$sAttId]) && $aSessionFilter[$sActCat][$sAttId] == $sAttValue;

                    $sAttValueId = md5( $sAttValue );

                    $blActiveFilter = $blActiveFilter || $oValue->blSelected;
                    $aAttributes[$sAttId]->aValues[$sAttValueId] = $oValue;
                    $rs->moveNext();
                }
            }

        }

        if ( is_array($aSessionFilter[$sActCat]) && !$blActiveFilter ) {
            oxSession::setVar( "session_attrfilter", false);
        }
        return $aAttributes;
    }

    /**
     * Loads and returns category in base language
     *
     * @param object $oActCategory active category
     *
     * @return object
     */
    public function getCatInLang( $oActCategory = null )
    {
        $oCategoryInDefaultLanguage= oxNew( "oxcategory" );
        if ( $this->isPriceCategory() ) {
            // get it in base language
            $oCategoryInDefaultLanguage= oxNew( "oxcategory" );
            $oCategoryInDefaultLanguage->loadInLang( 0, $this->getId());
        } else {
            $oCategoryInDefaultLanguage= oxNew( "oxcategory" );
            $oCategoryInDefaultLanguage->loadInLang( 0, $oActCategory->getId());
        }
        return $oCategoryInDefaultLanguage;
    }

    /**
     * Parent category setter
     *
     * @param oxcategory $oCategory parent category object
     *
     * @return null
     */
    public function setParentCategory( $oCategory )
    {
        $this->_oParent = $oCategory;
    }

    /**
     * Returns parent category object for current category (is it is available)
     *
     * @return oxcategory
     */
    public function getParentCategory()
    {
        $oCat = null;

        // loading only if parent id is not rootid
        if ( $this->oxcategories__oxparentid->value && $this->oxcategories__oxparentid->value != 'oxrootid' ) {

            // checking if object itself has ref to parent
            if ( $this->_oParent ) {
                $oCat = $this->_oParent;
            } else {
                $oCat = oxNew( 'oxcategory' );
                if ( !$oCat->loadInLang( $this->getLanguage(), $this->oxcategories__oxparentid->value ) ) {
                    $oCat = null;
                }
            }
        }
        return $oCat;
    }

    /**
     * Returns root category id of a child category
     *
     * @param string $sCategoryId category id
     *
     * @return integer
     */
    public static function getRootId($sCategoryId)
    {
        if ( !isset( $sCategoryId ) ) {
            return;
        }

        return oxDb::getDb()->getOne( 'select oxrootid from '.getViewName('oxcategories').' where oxid = ?', array( $sCategoryId ) );
    }


    /**
     * Before assigning the record from SQL it checks for viewable rights
     *
     * @param string $sSelect SQL select
     *
     * @return bool
     */
    public function assignViewableRecord($sSelect)
    {
            if ( $this->assignRecord( $sSelect ) ) {
                return  true;
            }


        return false;
    }

    /**
     * Inserts new category (and updates existing node oxleft amd oxright accordingly). Returns true on success.
     *
     * @return bool
     */
    protected function _insert()
    {


        if ( $this->oxcategories__oxparentid->value != "oxrootid") {
            // load parent

            $oParent = oxNew( "oxcategory" );
            //#M317 check if parent is loaded
            if ( !$oParent->load( $this->oxcategories__oxparentid->value) ) {
                return false;
            }

            $sAdd = " and oxshopid = '" . $this->getShopId() . "' ";

            // update existing nodes
            $oDB = oxDb::getDb();
            $oDB->execute( "UPDATE oxcategories SET OXLEFT = OXLEFT + 2
                            WHERE  OXROOTID = '".$oParent->oxcategories__oxrootid->value."'
                            AND OXLEFT >   ".$oParent->oxcategories__oxright->value."
                            AND OXRIGHT >= ".$oParent->oxcategories__oxright->value.$sAdd);


            $oDB->execute( "UPDATE oxcategories SET OXRIGHT = OXRIGHT + 2
                            WHERE  OXROOTID = '".$oParent->oxcategories__oxrootid->value."'
                            AND OXRIGHT >= ".$oParent->oxcategories__oxright->value.$sAdd );

            //if ( !isset( $this->_sOXID) || trim( $this->_sOXID) == "")
            //    $this->_sOXID = oxUtilsObject::getInstance()->generateUID();
            //$this->oxcategories__oxid->setValue($this->_sOXID);
            //refactored to:
            if ( !$this->getId() ) {
                $this->setId();
            }

            $this->oxcategories__oxrootid = new oxField($oParent->oxcategories__oxrootid->value, oxField::T_RAW);
            $this->oxcategories__oxleft = new oxField($oParent->oxcategories__oxright->value, oxField::T_RAW);
            $this->oxcategories__oxright = new oxField($oParent->oxcategories__oxright->value + 1, oxField::T_RAW);
            return parent::_insert();
        } else {
            // root entry
            if ( !$this->getId() ) {
                $this->setId();
            }

            $this->oxcategories__oxrootid = new oxField($this->getId(), oxField::T_RAW);
            $this->oxcategories__oxleft = new oxField(1, oxField::T_RAW);
            $this->oxcategories__oxright = new oxField(2, oxField::T_RAW);
            return parent::_insert();
        }
    }

    /**
     * Updates category tree, returns true on success.
     *
     * @return bool
     */
    protected function _update()
    {

        $oDB = oxDb::getDb();

        $sOldParentID = $oDB->getOne( "select oxparentid from oxcategories where oxid = '".$this->getId()."'");

        if ( $this->_blIsSeoObject && $this->isAdmin() ) {
            oxSeoEncoderCategory::getInstance()->markRelatedAsExpired($this);
        }

        $blRes = parent::_update();

        // #872C - need to update category tree oxleft and oxright values (nested sets),
        // then sub trees are moved inside one root, or to another root.
        // this is done in 3 basic steps
        // 1. increase oxleft and oxright values of target root tree by $iTreeSize, where oxleft>=$iMoveAfter , oxright>=$iMoveAfter
        // 2. modify current subtree, we want to move by adding $iDelta to it's oxleft and oxright,  where oxleft>=$sOldParentLeft and oxright<=$sOldParentRight values,
        //    in this step we also modify rootid's if they were changed
        // 3. decreasing oxleft and oxright values of current root tree, where oxleft >= $sOldParentRight+1 , oxright >= $sOldParentRight+1

        // did we change position in tree ?
        if ( $this->oxcategories__oxparentid->value != $sOldParentID) {
            $sOldParentLeft = $this->oxcategories__oxleft->value;
            $sOldParentRight = $this->oxcategories__oxright->value;

            $iTreeSize = $sOldParentRight-$sOldParentLeft+1;

            $sNewRootID = $oDB->getOne( "select oxrootid from oxcategories where oxid = '".$this->oxcategories__oxparentid->value."'");

            //If empty rootID, we set it to categorys oxid
            if ( $sNewRootID == "") {
                //echo "<br>* ) Creating new root tree ( {$this->_sOXID} )";
                $sNewRootID = $this->getId();
            }

            $sNewParentLeft = $oDB->getOne( "select oxleft from oxcategories where oxid = '".$this->oxcategories__oxparentid->value."'");

            //if(!$sNewParentLeft){
                //the current node has become root node, (oxrootid == "oxrootid")
            //    $sNewParentLeft = 0;
            //}

            $iMoveAfter = $sNewParentLeft+1;


            //New parentid can not be set to it's child
            if ($sNewParentLeft > $sOldParentLeft && $sNewParentLeft < $sOldParentRight && $this->oxcategories__oxrootid->value == $sNewRootID) {
                //echo "<br>* ) Can't asign category to it's child";

                //Restoring old parentid, stoping further actions
                $sRestoreOld = "UPDATE oxcategories SET OXPARENTID = '".$sOldParentID."' WHERE oxid = '".$this->getId()."'";
                $oDB->execute( $sRestoreOld );
                return false;
            }

            //Old parent will be shifted too, if it is in the same tree
            if ($sOldParentLeft > $iMoveAfter && $this->oxcategories__oxrootid->value == $sNewRootID) {
                $sOldParentLeft += $iTreeSize;
                $sOldParentRight += $iTreeSize;
            }

            $iDelta = $iMoveAfter-$sOldParentLeft;

            //echo "Size=$iTreeSize, NewStart=$iMoveAfter, delta=$iDelta";

            $sAddOld = " and oxshopid = '" . $this->getShopId() . "' and OXROOTID = '".$this->oxcategories__oxrootid->value."';";
            $sAddNew = " and oxshopid = '" . $this->getShopId() . "' and OXROOTID = '".$sNewRootID."';";

            //Updating everything after new position
            $oDB->execute( "UPDATE oxcategories SET OXLEFT = (OXLEFT + ".$iTreeSize.") WHERE OXLEFT >= ".$iMoveAfter.$sAddNew );
            $oDB->execute( "UPDATE oxcategories SET OXRIGHT = (OXRIGHT + ".$iTreeSize.") WHERE OXRIGHT >= ".$iMoveAfter.$sAddNew );
            //echo "<br>1.) + $iTreeSize, >= $iMoveAfter";

            $sChangeRootID = "";
            if ($this->oxcategories__oxrootid->value != $sNewRootID) {
                //echo "<br>* ) changing root IDs ( {$this->oxcategories__oxrootid->value} -> {$sNewRootID} )";
                $sChangeRootID = ", OXROOTID='$sNewRootID'";
            }

            //Updating subtree
            $oDB->execute( "UPDATE oxcategories SET OXLEFT = (OXLEFT + ".$iDelta."), OXRIGHT = (OXRIGHT + ".$iDelta.") ".$sChangeRootID." WHERE OXLEFT >= ".$sOldParentLeft." AND OXRIGHT <= ".$sOldParentRight.$sAddOld );
            //echo "<br>2.) + $iDelta, >= $sOldParentLeft and <= $sOldParentRight";

            //Updating everything after old position
            $oDB->execute( "UPDATE oxcategories SET OXLEFT = (OXLEFT - ".$iTreeSize.") WHERE OXLEFT >=   ".($sOldParentRight+1).$sAddOld );
            $oDB->execute( "UPDATE oxcategories SET OXRIGHT = (OXRIGHT - ".$iTreeSize.") WHERE OXRIGHT >=   ".($sOldParentRight+1).$sAddOld );
            //echo "<br>3.) - $iTreeSize, >= ".($sOldParentRight+1);
        }

        if ( $blRes && $this->_blIsSeoObject && $this->isAdmin() ) {
            oxSeoEncoderCategory::getInstance()->markRelatedAsExpired($this);
        }

        return $blRes;
    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData( $sFieldName, $sValue, $iDataType = oxField::T_TEXT)
    {
        //preliminar quick check saves 3% of execution time in category lists by avoiding redundant strtolower() call
        if ($sFieldName[2] == 'l' || $sFieldName[2] == 'L' || (isset($sFieldName[16]) && ($sFieldName[16] == 'l' || $sFieldName[16] == 'L') ) ) {
            if ('oxlongdesc' === strtolower($sFieldName) || 'oxcategories__oxlongdesc' === strtolower($sFieldName)) {
                $iDataType = oxField::T_RAW;
            }
        }
        return parent::_setFieldData($sFieldName, $sValue, $iDataType);
    }


    /**
     * Returns category icon picture
     *
     * @return string
     */
    public function getIconUrl()
    {
        return $this->getConfig()->getPictureUrl( 'icon/'.$this->oxcategories__oxicon->value );
    }

    /**
     * Returns true is category parent id is 'oxrootid'
     *
     * @return bool
     */
    public function isTopCategory()
    {
        if ( $this->_blTopCategory == null ) {
            $this->_blTopCategory = $this->oxcategories__oxparentid->value == 'oxrootid';
        }
        return $this->_blTopCategory;
    }

    /**
     * Returns true if current category is price type ( ( oxpricefrom || oxpriceto ) > 0 )
     *
     * @return bool
     */
    public function isPriceCategory()
    {
        return (bool) ( $this->oxcategories__oxpricefrom->value || $this->oxcategories__oxpriceto->value );
    }
}
