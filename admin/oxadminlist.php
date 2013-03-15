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
 * @package   admin
 * @copyright (C) OXID eSales AG 2003-2010
 * @version OXID eShop CE
 * @version   SVN: $Id: oxadminlist.php 25640 2010-02-05 06:42:24Z alfonsas $
 */

/**
 * Admin selectlist list manager.
 * @package admin
 */
class oxAdminList extends oxAdminView
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = null;

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxlist';

    /**
     * List of objects (default null).
     *
     * @var object
     */
    protected $_oList = null;

    /**
     * Position in list of objects (default 0).
     *
     * @var integer
     */
    protected $_iCurrListPos = 0;

    /**
     * Size of objetc list (default 0).
     *
     * @var integer
     */
    protected $_iListSize = 0;

    /**
     * Array of SQL query conditions (default null).
     *
     * @var array
     */
    protected $_aWhere = null;

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSort = null;

    /**
     * Sorting parameters
     *
     * @var array
     */
    protected $_aSort = array();

    /**
     * Enable/disable sorting by DESC (SQL) (defaultfalse - disable).
     *
     * @var bool
     */
    protected $_blDesc = false;

    /**
     * Set to true to enable multilanguage
     *
     * @var object
     */
    protected $_blEmployMultilanguage = null;

    /**
     * (default null).
     *
     * @var integer
     */
    protected $_iOverPos = null;

    /**
     * Viewable list size
     *
     * @var int
     */
    protected $_iViewListSize = 0;

    /**
     * Viewable default list size (used in list_*.php views)
     *
     * @var int
     */
    protected $_iDefViewListSize = 50;

    /**
     * Viewable list size getter
     *
     * @return int
     */
    protected function _getViewListSize()
    {
        if ( !$this->_iViewListSize ) {
            $myConfig = $this->getConfig();
            if ( $aProfile = oxSession::getVar( 'profile' ) ) {
                if ( isset( $aProfile[1] ) ) {
                    $myConfig->setConfigParam( 'iAdminListSize', (int) $aProfile[1] );
                }
            }

            $this->_iViewListSize = (int) $myConfig->getConfigParam( 'iAdminListSize' );
            if ( !$this->_iViewListSize ) {
                $this->_iViewListSize = 10;
                $myConfig->setConfigParam( 'iAdminListSize', $this->_iViewListSize );
            }
        }

        return $this->_iViewListSize;
    }

    /**
     * Viewable list size getter (used in list_*.php views)
     *
     * @return int
     */
    protected function _getUserDefListSize()
    {
        if ( !$this->_iViewListSize ) {
            if ( ! ($iViewListSize = (int) oxConfig::getParameter( 'viewListSize' ) ) ) {
                $iViewListSize = $this->_iDefViewListSize;
            }
            $this->_iViewListSize = $iViewListSize;
        }

        return $this->_iViewListSize;
    }

    /**
     * Executes parent::init(), loads list items
     *
     * @return null
     */
    public function init()
    {
        parent::init();

        $myConfig = $this->getConfig();

        if ( $this->_sListClass ) {

            $this->_oList = oxNew( $this->_sListType, 'core' );
            $this->_oList->clear();
            $this->_oList->init( $this->_sListClass );

            $aWhere = $this->buildWhere();

            $oListObject = $this->_oList->getBaseObject();

            oxSession::setVar( 'tabelle', $this->_sListClass );
            $this->_aViewData['listTable'] = getViewName( $oListObject->getCoreTableName() );
            $myConfig->setGlobalParameter( 'ListCoreTable', $oListObject->getCoreTableName() );

            if ( $oListObject->isMultilang() ) {
                // is the object multilingual?
                $oListObject->setLanguage( oxLang::getInstance()->getBaseLanguage() );

                if ( isset( $this->_blEmployMultilanguage ) ) {
                    $oListObject->setEnableMultilang( $this->_blEmployMultilanguage );
                }
            }

            $sSql = $this->_buildSelectString( $oListObject );
            $sSql = $this->_prepareWhereQuery( $aWhere, $sSql );
            $sSql = $this->_prepareOrderByQuery( $sSql );
            $sSql = $this->_changeselect( $sSql );

            // calculates count of list items
            $this->_calcListItemsCount( $sSql );

            // setting current list position (page)
            $this->_setCurrentListPosition( oxConfig::getParameter( 'jumppage' ) );

            // settting additioan params for list: current list size
            $this->_oList->setSqlLimit( $this->_iCurrListPos, $this->_getViewListSize() );

            $this->_oList->selectString( $sSql );
        }
    }

    /**
     * Executes parent::render(), sets back search keys to view, sets navigation params
     *
     * @return null
     */
    public function render()
    {
        $sReturn = parent::render();

        // assign our list
        if ( $this->_oList ) {
            $this->_aViewData['mylist'] = $this->_oList;
        }

        // setting filter data back to view
        $this->_setFilterParams();

        // set navigation parameters
        $this->_setListNavigationParams();

        // sorting
        $this->_aSort[0] = oxConfig::getParameter( 'sort' );
        if ( !isset( $this->_aSort[0]) || !$this->_aSort[0] )
            $this->_aSort[0] = $this->_sDefSort;

        $this->_aViewData['sort'] = $this->_aSort[0];

        return $sReturn;
    }

    /**
     * Sets view filter data
     *
     *  - aViewData['where'] containts filter data like $object->oxarticles__oxtitle = filter_value
     *  - aViewData['whereparam'] contains string which can be later used in url. and
     *    looks like &amp;where[oxarticles.oxtitle]=_filter_value_&amp;art_category=_filter_categry_;
     *
     * @return null
     */
    protected function _setFilterParams()
    {
        // build where
        if ( is_array( $aWhere = oxConfig::getParameter( 'where' ) ) ) {

            $myConfig = $this->getConfig();
            $myUtils  = oxUtils::getInstance();
            $sListTable = $myConfig->getGlobalParameter( 'ListCoreTable' );

            $oSearchKeys = new oxStdClass();
            $sWhereParam = "";

            while ( list( $sName, $sValue ) = each( $aWhere ) ) {
                $sWhereParam .= "&amp;where[".$sName."]=".$sValue;
                $sFieldName = str_replace( getViewName( $sListTable ) . '.', $sListTable . '.', $sName );
                $sFieldName = $myUtils->getArrFldName( $sFieldName );
                $oSearchKeys->$sFieldName = $sValue;
            }

            $this->_aViewData['where'] = $oSearchKeys;

            //#M430: Pagination in admin list loses category parameter
            if ( $sChosenCat  = oxConfig::getParameter( "art_category") ) {
                $sWhereParam .= "&amp;art_category=".$sChosenCat;
            }
            $this->_aViewData['whereparam'] = $sWhereParam;
        }
    }

    /**
     * Deletes this entry from the database
     *
     * @return null
     */
    public function deleteEntry()
    {
        $oDelete = oxNew( $this->_sListClass );


        $blDelete = $oDelete->delete( oxConfig::getParameter( 'oxid' ) );

        // #A - we must reset object ID
        if ( $blDelete && isset( $_POST['oxid'] ) ) {
            $_POST['oxid'] = -1;
        }


        $this->init();
    }

    /**
     * Calculates list items count
     *
     * @param string $sSql SQL query used co select list items
     *
     * @return null
     */
    protected function _calcListItemsCount( $sSql )
    {
        // count SQL
        $sSql = preg_replace( '/select .* from/', 'select count(*) from ', $sSql );

        // removing order by
        $sSql = preg_replace( '/order by .*$/', '', $sSql );

        // con of list items which fits current search conditions
        $this->_iListSize = oxDb::getDb()->getOne( $sSql );

        // set it into session that other frames know about size of DB
        oxSession::setVar( 'iArtCnt', $this->_iListSize );
    }

     /**
     * Set current list position
     *
     * @param string $sPage jump page string
     *
     * @return null
     */
    protected function _setCurrentListPosition( $sPage = null )
    {
        $iAdminListSize = $this->_getViewListSize();

        $iJumpTo = $sPage?( (int) $sPage):( (int) ( (int) oxConfig::getParameter( 'lstrt' ) ) / $iAdminListSize );
        $iJumpTo = ( $sPage && $iJumpTo )?( $iJumpTo - 1 ):$iJumpTo;

        $iJumpTo = $iJumpTo * $iAdminListSize;
        if ( $iJumpTo < 1 ) {
            $iJumpTo = 0;
        } elseif ( $iJumpTo >= $this->_iListSize ) {
            $iJumpTo = floor( $this->_iListSize / $iAdminListSize - 1 ) * $iAdminListSize;
        }

        $this->_iCurrListPos = $this->_iOverPos = (int) $iJumpTo;
    }

    /**
     * Adds order by to SQL query string.
     *
     * @param string $sSql sql string
     *
     * @return string
     */
    protected function _prepareOrderByQuery( $sSql = null )
    {
        // sorting
        $this->_aSort[0] = oxConfig::getParameter( 'sort' );

        if ( !isset( $this->_aSort[0]) || !$this->_aSort[0] )
            $this->_aSort[0] = $this->_sDefSort;

        $blSortDesc = $this->_blDesc;

        // add sorting
        if ( $this->_aSort && isset($this->_aSort[0] ) ) {

            $blSortDesc = oxConfig::getParameter( 'adminorder' );

            if ( !isset( $blSortDesc ) ) {
                $blSortDesc = $this->_blDesc;
            }

            // only add order by at full sql not for count(*)
            $sSql .= ' order by ';
            $blSep = false;
            $iLang = null;

            $oListObject = $this->_oList->getBaseObject();

            if ( $oListObject->isMultilang() ) {
                $iLang = $oListObject->getLanguage();
            }

            $sTable = $oListObject->getCoreTableName();
            $sViewName = getViewName( $sTable );

            foreach ( $this->_aSort as $orderColumn ) {

                //V oxactive field search always DESC
                if ( $orderColumn == "oxactive" ) {
                    $blSortDesc = true;
                }

                // multilanguage sorting
                if ( $iLang ) {
                    $sObjectField = "{$sTable}__{$orderColumn}";
                    if ( $oListObject instanceof oxI18n ) {
                        if ( $oListObject->isMultilingualField($orderColumn) ) {
                            $orderColumn .= "_{$iLang}";
                        }
                    }
                }

                //add table name to column name if no table name found attached to column name
                if ( strpos( $orderColumn, '.' ) === false ) {
                    $orderColumn = $sViewName . '.' . $orderColumn;
                }

                $sSql  .= ( ( ( $blSep ) ? ', ' : '' ) ) . oxDb::getInstance()->escapeString( $orderColumn );
                $blSep  = true;
            }

            if ( $blSortDesc ) {
                $sSql .= ' desc ';
            }
        }

        return $sSql;
    }

    /**
     * Builds and returns SQL query string.
     *
     * @param object $oListObject list main object
     *
     * @return string
     */
    protected function _buildSelectString( $oListObject = null )
    {
        $sSql = '';

        if ( $oListObject ) {
            $oBase = oxNew( 'oxBase' );
            $oBase->init( $oListObject->getCoreTableName() );
            $sSql = $oBase->buildSelectString( null );
        }

        return $sSql;
    }


    /**
     * Prepares SQL where query according SQL condition array and attaches it to SQL end.
     * For each search value if german umlauts exist, adds them
     * and replaced by spec. char to query
     *
     * @param string $sFieldValue Filters
     *
     * @return string
     */
    protected function _processFilter( $sFieldValue )
    {
        //removing % symbols
        $sFieldValue = preg_replace( "/^%|%$/", "", trim( $sFieldValue ) );
        return preg_replace( "/\s+/", " ", $sFieldValue);
    }

    /**
     * Builds part of SQL query
     *
     * @param string $sVal            filter value
     * @param bool   $blIsSearchValue filter value type, true means surrount search key with '%'
     *
     * @return string
     */
    protected function _buildFilter( $sVal, $blIsSearchValue )
    {
        if ( $blIsSearchValue ) {
            //is search string, using LIKE
            $sQ = " like '%{$sVal}%' ";
        } else {
            //not search string, values must be equal
            $sQ = " = ".oxDb::getDb()->quote( $sVal )." ";
        }

        return $sQ;
    }

    /**
     * Checks if fulter contains wildcards like %
     *
     * @param string $sFieldValue filter value
     *
     * @return bool
     */
    protected function _isSearchValue( $sFieldValue )
    {
        //check if this is search string (conatains % sign at begining and end of string)
        $blIsSearchValue = false;
        if ( preg_match( '/^%/', $sFieldValue ) && preg_match( '/%$/', $sFieldValue ) ) {
            $blIsSearchValue = true;
        }

        //removing % symbols
        return $blIsSearchValue;
    }

    /**
     * Prepares SQL where query according SQL condition array and attaches it to SQL end.
     * For each search value if german umlauts exist, adds them
     * and replaced by spec. char to query
     *
     * @param array  $aWhere  SQL condition array
     * @param string $sqlFull SQL query string
     *
     * @return string
     */
    protected function _prepareWhereQuery( $aWhere, $sqlFull )
    {
        if ( count($aWhere) ) {
            $myUtilsString = oxUtilsString::getInstance();
            while ( list($sFieldName, $sFieldValue) = each( $aWhere ) ) {
                $sFieldValue = trim( $sFieldValue );

                //check if this is search string (conatains % sign at begining and end of string)
                $blIsSearchValue = $this->_isSearchValue( $sFieldValue );

                //removing % symbols
                $sFieldValue = $this->_processFilter( $sFieldValue );

                if ( strlen($sFieldValue) ) {
                    $aVal = explode( ' ', $sFieldValue );

                    //for each search field using AND anction
                    $sSqlBoolAction = ' and (';

                    foreach ( $aVal as $sVal) {

                        $sFieldName = oxDb::getInstance()->escapeString( $sFieldName );
                        $sqlFull .= " {$sSqlBoolAction} {$sFieldName} ";

                        //for search in same field for different values using AND
                        $sSqlBoolAction = ' and ';

                        $sqlFull .= $this->_buildFilter( $sVal, $blIsSearchValue );

                        // trying to search spec chars in search value
                        // if found, add cleaned search value to search sql
                        $sUml = $myUtilsString->prepareStrForSearch( $sVal );
                        if ( $sUml ) {
                            $sqlFull .= " or {$sFieldName} ";

                            $sqlFull .= $this->_buildFilter( $sUml, $blIsSearchValue );
                        }
                    }

                    // end for AND action
                    $sqlFull .= ' ) ';
                }
            }
        }

        return $sqlFull;
    }

    /**
     * Override this for individual search in admin.
     *
     * @param string $sSql SQL select to change
     *
     * @return string
     */
    protected function _changeselect( $sSql )
    {
        return $sSql;
    }


    /**
     * Builds and returns array of SQL WHERE conditions.
     *
     * @return array
     */
    public function buildWhere()
    {
        $myConfig = $this->getConfig();
        $this->_aWhere = array();

        $iLanguage = oxLang::getInstance()->getBaseLanguage();
        $aWhere    = oxConfig::getParameter( 'where' );

        if ( !$this->_oList )
            return $this->_aWhere;

        $oListObject = $this->_oList->getBaseObject();
        $sTable = $oListObject->getCoreTableName();
        $sViewName = getViewName( $sTable );

        if ( $this->_oList && is_array( $aWhere ) ) {

            foreach ( $aWhere as $sName => $sValue ) {
                if ( $sValue || '0' === ( string ) $sValue ) {

                    if ( strpos( $sName, '.' ) === false ) {
                        // if no table name attached to field name, add it
                        $sName = "{$sTable}.{$sName}";
                    }


                    // test if field is multilang
                    if ( $oListObject instanceof oxI18n ) {
                        $sFldName = strtolower( preg_replace('/(.+)\./', '', $sName ) );
                        if ( $oListObject->isMultilingualField( $sFldName ) && $iLanguage ) {
                            $sName .=  "_$iLanguage";
                        }
                    }

                    // #M1260: if field is date
                    $sLocalDateFormat = $this->getConfig()->getConfigParam( 'sLocalDateFormat' );
                    if ( $sLocalDateFormat && $sLocalDateFormat != 'ISO') {
                        $sFldName = strtolower( preg_replace('/(.+)\./', '', $sName ) );
                        $sLongName = $sTable."__".$sFldName;
                        $sFldType = $oListObject->$sLongName->fldtype;
                        if ( $sFldType && ( $sFldType == "datetime" || $sFldType == "date" ) ) {
                            $sValue = $this->_convertToDBDate( $sValue, $sFldType );
                        }
                    }

                    $this->_aWhere[$sName] = "%{$sValue}%";
                }
            }
        }

        return $this->_aWhere;
    }

    /**
     * Converts date/datetime values to DB scheme (#M1260)
     *
     * @param string $sValue   field value
     * @param string $sFldType field type
     *
     * @return string
     */
    protected function _convertToDBDate( $sValue, $sFldType )
    {
        $oConvObject = new oxField();
        $oConvObject->setValue($sValue);
        if ( $sFldType == "datetime" ) {
            if ( strlen($sValue) == 10 || strlen($sValue) == 22 || ( strlen($sValue) == 19 && !stripos( $sValue, "m" ) ) ) {
                oxDb::getInstance()->convertDBDateTime( $oConvObject, true );
            } else {
                if ( strlen($sValue) > 10 ) {
                    return $this->_convertTime( $sValue );
                } else {
                    return $this->_convertDate( $sValue );
                }
            }
        } elseif ( $sFldType == "date" ) {
            if ( strlen($sValue) == 10 ) {
                oxDb::getInstance()->convertDBDate( $oConvObject, true);
            } else {
                return $this->_convertDate( $sValue );
            }
        }
        return $oConvObject->value;
    }

    /**
     * Converter for date field search. If not full date will be searched.
     *
     * @param string $sDate searched date
     *
     * @return string
     */
    protected function _convertDate( $sDate )
    {
        // regexps to validate input
        $aDatePatterns = array("/^([0-9]{2})\.([0-9]{4})/" => "EUR2",    // MM.YYYY
                               "/^([0-9]{2})\.([0-9]{2})/" => "EUR1",    // DD.MM
                               "/^([0-9]{2})\/([0-9]{4})/" => "USA2",    // MM.YYYY
                               "/^([0-9]{2})\/([0-9]{2})/" => "USA1"     // DD.MM
                              );

        // date/time formatting rules
        $aDFormats  = array("EUR1" => array(2, 1),
                            "EUR2" => array(2, 1),
                            "USA1" => array(1, 2),
                            "USA2" => array(2, 1)
                           );

        // looking for date field
        $aDateMatches = array();
        foreach ( $aDatePatterns as $sPattern => $sType) {
            if ( preg_match( $sPattern, $sDate, $aDateMatches)) {
                $sDate = $aDateMatches[$aDFormats[$sType][0]] . "-" . $aDateMatches[$aDFormats[$sType][1]];
                break;
            }
        }

        return $sDate;
    }

    /**
     * Converter for datetime field search. If not full time will be searched.
     *
     * @param string $sFullDate searched date
     *
     * @return string
     */
    protected function _convertTime( $sFullDate )
    {
        $sDate = substr( $sFullDate, 0, 10 );
        $oConvObject = new oxField();
        $oConvObject->setValue($sDate);
        oxDb::getInstance()->convertDBDate( $oConvObject, true);

        // looking for time field
        $sTime = substr( $sFullDate, 11);
        if ( preg_match( "/([0-9]{2}):([0-9]{2}) ([AP]{1}[M]{1})$/", $sTime, $aTimeMatches ) ) {
            if ( $aTimeMatches[3] == "PM") {
                $iIntVal = (int) $aTimeMatches[1];
                if ( $iIntVal < 13) {
                    $sTime = ($iIntVal + 12) . ":" . $aTimeMatches[2];
                }
            } else {
                $sTime = $aTimeMatches[1] . ":" . $aTimeMatches[2];
            }
        } elseif ( preg_match( "/([0-9]{2}) ([AP]{1}[M]{1})$/", $sTime, $aTimeMatches ) ) {
            if ( $aTimeMatches[2] == "PM") {
                $iIntVal = (int) $aTimeMatches[1];
                if ( $iIntVal < 13) {
                    $sTime = ($iIntVal + 12);
                }
            } else {
                $sTime = $aTimeMatches[1];
            }
        } else {
            $sTime = str_replace(".", ":", $sTime);
        }

        return $oConvObject->value . " " . $sTime;
    }

    /**
     * Set parameters needed for list navigation
     *
     * @return null
     */
    protected function _setListNavigationParams()
    {
        $myConfig  = $this->getConfig();

        // list navigation
        $blShowNavigation = false;
        $iAdminListSize = $this->_getViewListSize();
        if ( $this->_iListSize > $iAdminListSize ) {
            // yes, we need to build the navigation object
            $pagenavigation = new oxStdClass();
            $pagenavigation->pages    = round( ( ( $this->_iListSize - 1 ) / $iAdminListSize ) + 0.5, 0 );
            $pagenavigation->actpage  = ($pagenavigation->actpage > $pagenavigation->pages)? $pagenavigation->pages : round( ( $this->_iCurrListPos / $iAdminListSize ) + 0.5, 0 );
            $pagenavigation->lastlink = ( $pagenavigation->pages - 1 ) * $iAdminListSize;
            $pagenavigation->nextlink = null;
            $pagenavigation->backlink = null;

            $iPos = $this->_iCurrListPos + $iAdminListSize;
            if ( $iPos < $this->_iListSize ) {
                $pagenavigation->nextlink = $iPos = $this->_iCurrListPos + $iAdminListSize;
            }

            if ( ( $this->_iCurrListPos - $iAdminListSize ) >= 0 ) {
                $pagenavigation->backlink = $iPos = $this->_iCurrListPos - $iAdminListSize;
            }

            // calculating list start position
            $iStart = $pagenavigation->actpage - 5;
            $iStart = ( $iStart <= 0 ) ? 1 : $iStart;

            // calculating list end position
            $iEnd = $pagenavigation->actpage + 5;
            $iEnd = ( $iEnd < $iStart + 10) ? $iStart + 10 : $iEnd;
            $iEnd = ( $iEnd > $pagenavigation->pages ) ? $pagenavigation->pages : $iEnd;

            // once again adjusting start pos ..
            $iStart = ( $iEnd - 10 > 0 ) ? $iEnd - 10 : $iStart;
            $iStart = ( $pagenavigation->pages <= 11) ? 1 : $iStart;

            // navigation urls
            for ( $i = $iStart; $i <= $iEnd; $i++ ) {
                $page = new Oxstdclass();
                $page->selected = 0;
                if ( $i == $pagenavigation->actpage ) {
                    $page->selected = 1;
                }
                $pagenavigation->changePage[$i] = $page;
            }

            $this->_aViewData['pagenavi'] = $pagenavigation;

            if ( isset( $this->_iOverPos)) {
                $iPos = $this->_iOverPos;
                $this->_iOverPos = null;
            } else {
                $iPos = oxConfig::getParameter( 'lstrt' );
            }

            if ( !$iPos ) {
                $iPos = 0;
            }

            $this->_aViewData['lstrt']    = $iPos;
            $this->_aViewData['listsize'] = $this->_iListSize;
            $blShowNavigation = true;
        }

        // determine not used space in List
        $iShowListSize  = $this->_iListSize - $this->_iCurrListPos;
        $iAdminListSize = $this->_getViewListSize();
        $iNotUsed = $iAdminListSize - min( $iShowListSize, $iAdminListSize );
        $iSpace = $iNotUsed * 15;

        if ( !$blShowNavigation ) {
            $iSpace += 20;
        }

        $this->_aViewData['iListFillsize'] = $iSpace;
    }

    /**
     * Sets-up navigation parameters
     *
     * @param string $sNode active view id
     *
     * @return null
     */
    protected function _setupNavigation( $sNode )
    {
        // navigation according to class
        if ( $sNode ) {

            $myAdminNavig = $this->getNavigation();

            $sOxId = oxConfig::getParameter( 'oxid' );

            if ( $sOxId == -1) {
                //on first call or when pressed creating new item button, reseting active tab
                $iActTab = $this->_iDefEdit;
            } else {
                // active tab
                $iActTab = oxConfig::getParameter( 'actedit' );
                $iActTab = $iActTab ? $iActTab : $this->_iDefEdit;
            }

            // tabs
            $this->_aViewData['editnavi'] = $myAdminNavig->getTabs( $sNode, $iActTab );

            // active tab
            $this->_aViewData['actlocation'] = $myAdminNavig->getActiveTab( $sNode, $iActTab );

            // default tab
            $this->_aViewData['default_edit'] = $myAdminNavig->getActiveTab( $sNode, $this->_iDefEdit );

            // passign active tab number
            $this->_aViewData['actedit'] = $iActTab;
        }
    }

    /**
     * Returns items list
     *
     * @return oxlist
     */
    public function getItemList()
    {
        return $this->_oList;
    }
}
