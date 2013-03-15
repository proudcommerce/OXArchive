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
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2011
 * @version OXID eShop CE
 * @version   SVN: $Id: oxlist.php 33836 2011-03-18 11:06:18Z sarunas $
 */

/**
 * List manager.
 * Collects list data (eg. from DB), performs list changes updating (to DB), etc.
 * @package core
 */
class oxList extends oxSuperCfg implements ArrayAccess, Iterator, Countable
{
    /**
     * Array of objects (some object list).
     *
     * @var array $_aArray
     */
    protected $_aArray = array();

    /**
     * Template object used for some methods before the list is built.
     *
     * @var oxBase
     */
    private $_oBaseObject = null;

    /**
     * Flag if array is ok or not
     *
     * @var boolean $_blValid
     */
    private $_blValid = true;

    /**
     * -----------------------------------------------------------------------------------------------------
     *
     * Implementation of SPL Array classes functions follows here
     *
     * -----------------------------------------------------------------------------------------------------
     */

    /**
     * implementation of abstract classes for ArrayAccess follow
     */
    /**
     * offsetExists for SPL
     *
     * @param mixed $offset SPL array offset
     *
     * @return boolean
     */
    public function offsetExists( $offset )
    {
        if ( isset( $this->_aArray[$offset] ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * offsetGet for SPL
     *
     * @param mixed $offset SPL array offset
     *
     * @return oxBase
     */
    public function offsetGet( $offset )
    {
        if ( $this->offsetExists( $offset ) ) {
            return $this->_aArray[$offset];
        } else {
            return false;
        }
    }

    /**
     * offsetSet for SPL
     *
     * @param mixed  $offset SPL array offset
     * @param oxBase $oBase  Array element
     *
     * @return null;
     */
    public function offsetSet( $offset, $oBase )
    {
        if ( isset( $offset ) ) {
            $this->_aArray[$offset] = & $oBase;
        } else {
            $sLongFieldName = $this->_getFieldLongName( 'oxid' );
            if ( isset( $oBase->$sLongFieldName->value ) ) {
                $sOxid = $oBase->$sLongFieldName->value;
                $this->_aArray[$sOxid] = & $oBase;
            } else {
                $this->_aArray[] = & $oBase;
            }
        }

    }

    /**
     * offsetUnset for SPL
     *
     * @param mixed $offset SPL array offset
     *
     * @return null;
     */
    public function offsetUnset( $offset )
    {
        unset( $this->_aArray[$offset] );
    }

    /**
     * Returns SPL array keys
     *
     * @return array
     */
    public function arrayKeys()
    {
        return array_keys( $this->_aArray );
    }

    /**
     * rewind for SPL
     *
     * @return null;
     */
    public function rewind()
    {
        $this->_blValid = ( false !== reset( $this->_aArray ) );
    }

    /**
     * current for SPL
     *
     * @return null;
     */
    public function current()
    {
        return current( $this->_aArray );
    }

    /**
     * key for SPL
     *
     * @return mixed
     */
    public function key()
    {
        return key( $this->_aArray );
    }

    /**
     * next for SPL
     *
     * @return null;
     */
    public function next()
    {
        $this->_blValid = ( false !== next( $this->_aArray ) );
    }

    /**
     * valid for SPL
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_blValid;
    }

    /**
     * count for SPL
     *
     * @return integer
     */
    public function count()
    {
        return count( $this->_aArray );
    }

    /**
     * clears/destroys list contents
     *
     * @return null;
     */
    public function clear()
    {
        /*
        foreach ( $this->_aArray as $key => $sValue) {
            unset( $this->_aArray[$key]);
        }
        reset( $this->_aArray);*/
        $this->_aArray = array();
    }

    /**
     * copies a given array over the objects internal array (something like old $myList->aList = $aArray)
     *
     * @param array $aArray array of list items
     *
     * @return null
     */
    public function assign( $aArray )
    {
        $this->_aArray = $aArray;
    }

    /**
     * returns the array reversed, the internal array remains untouched
     *
     * @return array
     */
    public function reverse()
    {
        return array_reverse( $this->_aArray );
    }

    /**
     * -----------------------------------------------------------------------------------------------------
     * SPL implmentation end
     * -----------------------------------------------------------------------------------------------------
     */

    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxBase';

    /**
     * Core table name
     *
     * @var string
     */
    protected $_sCoreTable = null;

    /**
     * @var string ShopID
     */
    protected $_sShopID = null;

    /**
     * Holds a name of the method to be called on list object before executing assign()
     *
     * @var array/string
     *
     * @deprecated
     */
    protected $_aAssignCallbackPrepend  = null;

    /**
     * Holds a name of the method to be called on list object after executing assign()
     *
     * @var array/string
     *
     * @deprecated
     */
    protected $_aAssignCallback = null;

    /**
     * @var array SQL Limit, 0 => Start, 1 => Records
     */
    protected $_aSqlLimit = array();

    /**
     * Class Constructor
     *
     * @param string $sObjectName Associated list item object type
     */
    public function __construct( $sObjectName = null )
    {
        $myConfig = $this->getConfig();
        $this->_aSqlLimit[0] = 0;
        $this->_aSqlLimit[1] = 0;
        $this->_sShopID   = $myConfig->getShopId();

        if ( $sObjectName ) {
            $this->init( $sObjectName );
        }
    }

    /**
     * Backward compatibility method
     *
     * @param string $sName Variable name
     *
     * @return mixed
     */
    public function __get( $sName)
    {
        //throw new Exception( 'oxList Access to undefined variable '. $sName);
        //echo( "Access to ".$sName.PHP_EOL);

        // TMP
        if ( $sName == 'aList') {
            return $this->_aArray;
        }
    }

    /**
     * Returns list items array
     *
     * @return array
     */
    public function getArray()
    {
        return $this->_aArray;
    }

    /**
     * Inits list table name and object name.
     *
     * @param string $sObjectName List item object type
     * @param string $sCoreTable  Db table name this list s selected from
     *
     * @return null;
     */
    public function init($sObjectName, $sCoreTable = null)
    {
        $this->_sObjectsInListName = $sObjectName;
        if ($sCoreTable) {
            $this->_sCoreTable = $sCoreTable;
        }
    }

    /**
     * Sets function to be called on list object before loading it
     *
     * @param array $aAssignCallbackPrepend callback array
     *
     * @deprecated
     *
     * @return null
     */
    public function setAssignCallbackPrepend($aAssignCallbackPrepend)
    {
        $this->_aAssignCallbackPrepend = $aAssignCallbackPrepend;
    }

    /**
     * Sets function to be called on list object before loading it
     *
     * @param array $aAssignCallBack callback array
     *
     * @deprecated
     *
     * @return null
     */
    public function setAssignCallback( $aAssignCallBack)
    {
        $this->_aAssignCallback = $aAssignCallBack;
    }

    /**
     * Initializes or returns existing list template object.
     *
     * @return oxBase
     */
    public function getBaseObject()
    {
        if ( !$this->_oBaseObject ) {
            $this->_oBaseObject = oxNew( $this->_sObjectsInListName );
            $this->_oBaseObject->setInList();
            $this->_oBaseObject->init( $this->_sCoreTable );
        }

        return $this->_oBaseObject;
    }

    /**
     * Selects and SQL, creates objects and assign them
     *
     * @param string $sSql SQL select statement
     *
     * @return null;
     */
    public function selectString( $sSql)
    {
        $this->clear();

        if ( $this->_aSqlLimit[0] || $this->_aSqlLimit[1]) {
            $rs = oxDb::getDb(true)->SelectLimit( $sSql, $this->_aSqlLimit[1], $this->_aSqlLimit[0]);
        } else {
            $rs = oxDb::getDb(true)->Execute( $sSql);
        }

        if ($rs != false && $rs->recordCount() > 0) {

            $oSaved = clone $this->getBaseObject();

            // this code is deprecated and will be removed:
            if ( $this->_aAssignCallbackPrepend && is_callable($this->_aAssignCallbackPrepend)) {
                call_user_func( $this->_aAssignCallbackPrepend, $oSaved);
            }
            // end of deprecated code

            while (!$rs->EOF) {

                $oListObject = clone $oSaved;

                $this->_assignElement($oListObject, $rs->fields);

                // this code is deprecated and will be removed:
                if ( $this->_aAssignCallback ) {
                    call_user_func( $this->_aAssignCallback, $oListObject );
                }
                // end of deprecated code

                if ($oListObject->getId()) {
                    $this->_aArray[$oListObject->getId()] = $oListObject;
                } else {
                    $this->_aArray[] = $oListObject;
                }

                $rs->moveNext();
            }
        }
    }

    /**
     * Sets SQL Limit
     *
     * @param integer $iStart   Start e.g. limit Start,xxxx
     * @param integer $iRecords Nr of Records e.g. limit xxx,Records
     *
     * @return null;
     */
    public function setSqlLimit( $iStart, $iRecords)
    {
        $this->_aSqlLimit[0] = $iStart;
        $this->_aSqlLimit[1] = $iRecords;
    }

    /**
     * Function checks if there is at least one object in the list which has the given value in the given field
     *
     * @param mixed  $oVal       The searched value
     * @param string $sFieldName The name of the field, give "oxid" will access the classname__oxid field
     *
     * @return boolean
     */
    public function containsFieldValue($oVal, $sFieldName)
    {
        $sFieldName = $this->_getFieldLongName($sFieldName);
        foreach ($this->_aArray as $obj) {
            if ($obj->{$sFieldName}->value == $oVal) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generic function for laoding the list
     *
     * @return null;
     */
    public function getList()
    {
        $oListObject =$this->getBaseObject();
        $sFieldList = $oListObject->getSelectFields();
        $sQ = "select $sFieldList from " . $oListObject->getViewName();
        if ( $sActiveSnippet = $oListObject->getSqlActiveSnippet() ) {
            $sQ .= " where $sActiveSnippet ";
        }
        $this->selectString($sQ);

        return $this;
    }

    /**
     * Executes assign() method on list object. This method is called in loop in oxList::selectString().
     * It is if you want to execute any functionality on every list ELEMENT after it is fully loaded (assigned).
     *
     * @param oxBase $oListObject List object (the one derived from oxBase)
     * @param array  $aDbFields   An array holding db field values (normaly the result of oxDb::Execute())
     *
     * @return null;
     */
    protected function _assignElement($oListObject, $aDbFields)
    {
        $oListObject->assign($aDbFields);
    }

    /**
     * Returns field long name
     *
     * @param string $sFieldName Field name
     *
     * @return string
     */
    protected function _getFieldLongName($sFieldName)
    {
        if ($this->_sCoreTable) {
            return $this->_sCoreTable . '__' . $sFieldName;
        }

        return $sFieldName;
    }

}
