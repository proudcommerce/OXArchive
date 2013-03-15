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
 * $Id: article_selection.inc.php 16302 2009-02-05 10:18:49Z rimvydas.paskevicius $
 */

$aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxtitle',   'oxselectlist', 1, 1, 0 ),
                                        array( 'oxident',   'oxselectlist', 1, 0, 0 ),
                                        array( 'oxvaldesc', 'oxselectlist', 1, 0, 0 ),
                                        array( 'oxid',      'oxselectlist', 0, 0, 1 )
                                        ),
                     'container2' => array(
                                        array( 'oxtitle',   'oxselectlist', 1, 1, 0 ),
                                        array( 'oxident',   'oxselectlist', 1, 0, 0 ),
                                        array( 'oxvaldesc', 'oxselectlist', 1, 0, 0 ),
                                        array( 'oxid',      'oxobject2selectlist', 0, 0, 1 )
                                        )
                    );
/**
 * Class controls article assignment to selection lists
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
        $sSLViewName = getViewName('oxselectlist');

        $sArtId      = oxConfig::getParameter( 'oxid' );
        $sSynchArtId = oxConfig::getParameter( 'synchoxid' );

        if ( $sArtId ) {
            // all categories article is in
            $sQAdd  = " from oxobject2selectlist left join $sSLViewName on $sSLViewName.oxid=oxobject2selectlist.oxselnid ";
            $sQAdd .= " where oxobject2selectlist.oxobjectid = '$sArtId' ";
        } else {
            $sQAdd  = " from $sSLViewName  where $sSLViewName.oxid not in ( select oxobject2selectlist.oxselnid from oxobject2selectlist left join $sSLViewName on $sSLViewName.oxid=oxobject2selectlist.oxselnid ";
            $sQAdd .= " where oxobject2selectlist.oxobjectid = '$sSynchArtId' ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes article selection lists.
     *
     * @return null
     */
    public function removesel()
    {
        $aChosenArt = $this->_getActionIds( 'oxobject2selectlist.oxid' );
        if ( oxConfig::getParameter( 'all' ) ) {

            $sQ = $this->_addFilter( "delete oxobject2selectlist.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( is_array( $aChosenArt ) ) {
            $sQ = "delete from oxobject2selectlist where oxobject2selectlist.oxid in ('" . implode( "', '", $aChosenArt ) . "') ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds selection lists to article.
     *
     * @return null
     */
    public function addsel()
    {
        $aAddSel = $this->_getActionIds( 'oxselectlist.oxid' );
        $soxId   = oxConfig::getParameter( 'synchoxid');

        // adding
        if ( oxConfig::getParameter( 'all' ) ) {
            $sSLViewName = getViewName('oxselectlist');
            $aAddSel = $this->_getAll( $this->_addFilter( "select $sSLViewName.oxid ".$this->_getQuery() ) );
        }

        if ( $soxId && $soxId != "-1" && is_array( $aAddSel ) ) {
            foreach ($aAddSel as $sAdd) {
                $oNew = oxNew( "oxbase" );
                $oNew->init( "oxobject2selectlist" );
                $oNew->oxobject2selectlist__oxobjectid = new oxField($soxId);
                $oNew->oxobject2selectlist__oxselnid   = new oxField($sAdd);
                $oNew->save();
            }
        }
    }
}
