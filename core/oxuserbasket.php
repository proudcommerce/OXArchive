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
 * $Id: oxuserbasket.php 22548 2009-09-22 13:50:42Z rimvydas.paskevicius $
 */

/**
 * Virtual basket manager class. Virtual baskets are user article lists which are stored in database (noticelists, wishlists).
 * The name of the class is left like this because of historic reasons.
 * It is more relevant to wishlist and noticelist than to shoping basket.
 * Collects shopping basket information, updates it (DB level), removes or adds
 * articles to it.
 * @package core
 */
class oxUserBasket extends oxBase
{

    /**
     * Array of fields which must be skipped when updating object data
     *
     * @var array
     */
    protected $_aSkipSaveFields = array('oxcreate');

    /**
     * Current object class name
     *
     * @var string
     */
    protected $_sClassName = 'oxUserbasket';

    /**
     * Array of basket items
     *
     * @var array
     */
    protected $_aBasketItems = null;

    /**
     * Marker if basket is newly created. This avoids empty basket storing to DB
     *
     * @var bool
     */
    protected $_blNewBasket = false;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxuserbaskets' );
    }

    /**
     * Inserts object data to DB, returns true on success.
     *
     * @return mixed
     */
    protected function _insert()
    {
        // marking basket as not new any more
        $this->_blNewBasket = false;

        if ( !isset( $this->oxuserbaskets__oxpublic->value ) ) {
            $this->oxuserbaskets__oxpublic = new oxField(1, oxField::T_RAW);
        }

        return parent::_insert();
    }

    /**
     * Sets basket as newly created. This usually means that it is not
     * yet stored in DB and will only be stored if some item is added
     *
     * @return null
     */
    public function setIsNewBasket()
    {
        $this->_blNewBasket = true;
    }

    /**
     * Returns an array of articles belonging to the Items in the basket
     *
     * @return array of oxArticle
     */
    public function getArticles()
    {
        $aRes = array();
        $aItems = $this->getItems();
        if ( is_array( $aItems ) ) {
            foreach ( $aItems as $sId => $oItem ) {
                $oArticle = $oItem->getArticle( $sId );
                $aRes[$this->_getItemKey($oArticle->getId(), $oItem->getSelList())] = $oArticle;
            }
        }
        return $aRes;
    }

    /**
     * Returns list of basket items
     *
     * @param bool $blReload if TRUE forces to reload list
     *
     * @return array of oxUserBasketItems
     */
    public function getItems( $blReload = false )
    {
        // cached ?
        if ( $this->_aBasketItems !== null && !$blReload ) {
            return $this->_aBasketItems;
        }

        // initializing
        $this->_aBasketItems = array();

        // loading basket items
        $oArticle  = oxNew( 'oxarticle' );
        $sViewName = $oArticle->getViewName();

        $sSelect  = "select oxuserbasketitems.* from oxuserbasketitems left join $sViewName on oxuserbasketitems.oxartid = $sViewName.oxid ";
        $sSelect .= 'and '.$oArticle->getSqlActiveSnippet().' ';
        $sSelect .= "where oxuserbasketitems.oxbasketid = '".$this->getId()."' and $sViewName.oxid is not null ";

        $oItems = oxNew( 'oxlist' );
        $oItems->init( 'oxuserbasketitem' );
        $oItems->selectstring( $sSelect );

        foreach ( $oItems as $oItem ) {
            $sKey = $this->_getItemKey( $oItem->oxuserbasketitems__oxartid->value, $oItem->getSelList() );
            $this->_aBasketItems[$sKey] = $oItem;
        }

        return $this->_aBasketItems;
    }

    /**
     * Creates and returns  oxuserbasketitem object
     *
     * @param string $sProductId Product Id
     * @param array  $aSelList   product select lists
     *
     * @return oxUserBasketItem
     */

    protected function _createItem( $sProductId, $aSelList = null )
    {
        $oNewItem = oxNew( 'oxuserbasketitem' );
        $oNewItem->oxuserbasketitems__oxartid    = new oxField($sProductId, oxField::T_RAW);
        $oNewItem->oxuserbasketitems__oxbasketid = new oxField($this->getId(), oxField::T_RAW);

        if ( !$aSelList ) {
            $oArticle = oxNew( 'oxArticle' );
            $oArticle->load( $sProductId );
            $aSelectLists = $oArticle->getSelectLists();
            if ( ( $iSelCnt = count( $aSelectLists ) ) ) {
                $aSelList = array_fill( 0, $iSelCnt, '0' );
            }
        }

        $oNewItem->setSelList( $aSelList );

        return $oNewItem;
    }


    /**
     * Searches for item in basket items array and returns it. If not item was
     * found - new item is created.
     *
     * @param string $sProductId product id, basket item id or basket item index
     * @param array  $aSelList   select lists
     *
     * @return oxUserBasketItem
     */
    public function getItem( $sProductId, $aSelList)
    {
        // loading basket item list
        $aItems   = $this->getItems();
        $sItemKey = $this->_getItemKey( $sProductId, $aSelList );

        $oItem = null;
        // returning existing item
        if ( isset( $aItems[$sProductId] )) {
            $oItem = $aItems[$sProductId];
        } elseif ( isset( $aItems[$sItemKey] ) ) {
            $oItem = $aItems[$sItemKey];
        } else {
            $oItem = $this->_createItem( $sProductId, $aSelList );
        }

        return $oItem;
    }

    /**
     * Returns unique item key according to its ID and user chosen select
     *
     * @param string $sProductId Product Id
     * @param array  $aSel       product select lists
     *
     * @return string
     */
    protected function _getItemKey( $sProductId, $aSel = null )
    {
        $aSel = ( $aSel != null) ? $aSel : array (0=>'0');
        return md5( $sProductId.'|'.serialize( $aSel ) );
    }

    /**
     * Returns current basket item count
     *
     * @param bool $blReload if TRUE forces to reload list
     *
     * @return int
     */
    public function getItemCount( $blReload = false )
    {
        return count( $this->getItems( $blReload ) );
    }

    /**
     * Method adds/removes user chosen article to/from his noticelist or wishlist. Returns total amount
     * of articles in list.
     *
     * @param string $sProductId Article ID
     * @param double $dAmount    Product amount
     * @param array  $aSel       product select lists
     * @param bool   $blOverride if true overrides $dAmount, else sums previous with current it
     *
     * @return integer
     */
    public function addItemToBasket( $sProductId = null, $dAmount = null, $aSel = null, $blOverride = false )
    {
        // basket info is only written in DB when something is in it
        if ( $this->_blNewBasket ) {
            $this->save();
        }

        if ( ( $oUserBasketItem = $this->getItem( $sProductId, $aSel ) ) ) {

            // if amount = 0 the means remove it
            if ( !$dAmount ) {

                $oUserBasketItem->delete();
                if ( isset($this->_aBasketItems[$this->_getItemKey($sProductId, $aSel)])) {
                    unset( $this->_aBasketItems[$this->_getItemKey($sProductId, $aSel)] );
                }

            } else { // updating object info and adding (if not yet added) item into basket items array
                if ( !$blOverride && !empty($oUserBasketItem->oxuserbasketitems__oxamount->value) ) {
                    $dAmount += $oUserBasketItem->oxuserbasketitems__oxamount->value;
                }

                $oUserBasketItem->oxuserbasketitems__oxamount = new oxField($dAmount, oxField::T_RAW);
                $oUserBasketItem->save();

                $this->_aBasketItems[$this->_getItemKey($sProductId, $aSel)] = $oUserBasketItem;
            }

            return $dAmount;
        }
    }

    /**
     * Deletes current basket history
     *
     * @param string $sOXID Object ID(default null)
     *
     * @return bool
     */
    public function delete( $sOXID = null )
    {
        if ( !$sOXID ) {
            $sOXID = $this->getId();
        }

        $blDelete = false;
        if ( $sOXID && ( $blDelete = parent::delete( $sOXID ) ) ) {
            // cleaning up related data
            $sQ = "delete from oxuserbasketitems where oxbasketid = " . oxDb::getDb()->quote( $sOXID );
            oxDb::getDb()->execute( $sQ );
        }
        return $blDelete;
    }

    /**
     * Checks if user basket is visible for current user (public or own basket)
     *
     * @return bool
     */
    public function isVisible()
    {
        $oActivUser = $this->getConfig()->getUser();
        $sActivUserId = null;
        if ($oActivUser)
            $sActivUserId = $oActivUser->getId();

        $blIsVisible = (bool) ($this->oxuserbaskets__oxpublic->value) ||
                              ($sActivUserId && ($this->oxuserbaskets__oxuserid->value == $sActivUserId));

        return $blIsVisible;
    }

}
