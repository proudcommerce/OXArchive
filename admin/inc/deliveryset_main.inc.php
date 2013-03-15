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
 * @package inc
 * @copyright (C) OXID eSales AG 2003-2009
 * @version OXID eShop CE
 * $Id: deliveryset_main.inc.php 17244 2009-03-16 15:17:48Z arvydas $
 */

$aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxtitle',      'oxdelivery', 1, 1, 0 ),
                                        array( 'oxaddsum',     'oxdelivery', 1, 0, 0 ),
                                        array( 'oxaddsumtype', 'oxdelivery', 0, 0, 0 ),
                                        array( 'oxid',         'oxdelivery', 0, 0, 1 )
                                        ),
                     'container2' => array(
                                        array( 'oxtitle',      'oxdelivery', 1, 1, 0 ),
                                        array( 'oxaddsum',     'oxdelivery', 1, 0, 0 ),
                                        array( 'oxaddsumtype', 'oxdelivery', 0, 0, 0 ),
                                        array( 'oxid',  'oxdel2delset', 0, 0, 1 )
                                        )
                    );
/**
 * Class manages deliveryset and delivery configuration
 */
class ajaxComponent extends ajaxListComponent
{
    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $sId      = oxConfig::getParameter( 'oxid' );
        $sSynchId = oxConfig::getParameter( 'synchoxid' );

        $sDeliveryViewName = getViewName('oxdelivery');

        // category selected or not ?
        if ( !$sId) {
            $sQAdd  = " from $sDeliveryViewName where 1 ";
        } else {
            $sQAdd  = " from $sDeliveryViewName left join oxdel2delset on oxdel2delset.oxdelid=$sDeliveryViewName.oxid ";
            $sQAdd .= "where oxdel2delset.oxdelsetid = '$sId' ";
        }

        if ( $sSynchId && $sSynchId != $sId ) {
            $sQAdd .= "and $sDeliveryViewName.oxid not in ( select $sDeliveryViewName.oxid from $sDeliveryViewName left join oxdel2delset on oxdel2delset.oxdelid=$sDeliveryViewName.oxid ";
            $sQAdd .= "where oxdel2delset.oxdelsetid = '$sSynchId' ) ";
        }

        return $sQAdd;
    }

    /**
     * Remove this delivery cost from these sets
     *
     * @return null
     */
    public function removefromset()
    {
        $aRemoveGroups = $this->_getActionIds( 'oxdel2delset.oxid' );
        if ( oxConfig::getParameter( 'all' ) ) {

            $sQ = $this->_addFilter( "delete oxdel2delset.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( $aRemoveGroups && is_array( $aRemoveGroups ) ) {
            $sQ = "delete from oxdel2delset where oxdel2delset.oxid in ('" . implode( "', '", $aRemoveGroups ) . "') ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds this delivery cost to these sets
     *
     * @return null
     */
    public function addtoset()
    {
        $aChosenSets = $this->_getActionIds( 'oxdelivery.oxid' );
        $soxId       = oxConfig::getParameter( 'synchoxid');

        // adding
        if ( oxConfig::getParameter( 'all' ) ) {
            $sDeliveryViewName = getViewName('oxdelivery');
            $aChosenSets = $this->_getAll( $this->_addFilter( "select $sDeliveryViewName.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aChosenSets ) ) {
            $oDb = oxDb::getDb();
            foreach ( $aChosenSets as $sChosenSet) {
                // check if we have this entry already in
                $sID = $oDb->GetOne("select oxid from oxdel2delset where oxdelid = '$sChosenSet' and oxdelsetid = '$soxId'");
                if ( !isset( $sID) || !$sID) {
                    $oDel2delset = oxNew( 'oxbase' );
                    $oDel2delset->init( 'oxdel2delset' );
                    $oDel2delset->oxdel2delset__oxdelid    = new oxField($sChosenSet);
                    $oDel2delset->oxdel2delset__oxdelsetid = new oxField($soxId);
                    $oDel2delset->save();
                }
            }
        }
    }
}
