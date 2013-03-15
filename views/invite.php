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
 * @package   views
 * @copyright (C) OXID eSales AG 2003-2010
 * @version OXID eShop CE
 * @version   SVN: $Id: suggest.php 26801 2010-03-24 14:46:21Z arvydas $
 */

/**
 * Article suggestion page.
 * Collects some article base information, sets default recomendation text,
 * sends suggestion mail to user.
 */
class Invite extends oxUBase
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'invite.tpl';

    /**
     * Required fields to fill before sending suggest email
     * @var array
     */
    protected $_aReqFields = array( 'rec_email', 'send_name', 'send_email', 'send_message', 'send_subject' );

    /**
     * CrossSelling articlelist
     * @var object
     */
    protected $_oCrossSelling = null;

    /**
     * Similar products articlelist
     * @var object
     */
    protected $_oSimilarProducts = null;

    /**
     * Recommlist
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * Invition data
     * @var object
     */
    protected $_aInviteData = null;

    /**
     * Class handling CAPTCHA image.
     * @var object
     */
    protected $_oCaptcha = null;

    /**
     * Email sent status status.
     * @var integer
     */
    protected $_iMailStatus = null;


    /**
     * Loads and passes article and related info to template engine
     * (oxarticle::getReviews(), oxarticle::getCrossSelling(),
     * oxarticle::GetSimilarProducts()), executes parent::render()
     * and returns template file name to render suggest::_sThisTemplate.
     *
     * Template variables:
     * <b>product</b>, <b>reviews</b>, <b>crossselllist</b>,
     * <b>similarlist</b>
     *
     * @return  string  current template file name
     */
    public function render()
    {
        parent::render();

        //getting captcha
        $this->_aViewData['oCaptcha'] = $this->getCaptcha();

        $this->_aViewData['editval'] = $this->getInviteData();

        //checking if email was sent
        if ( $this->_iMailStatus == 1 ) {
            $this->_aViewData['success'] = true;
        }

        return $this->_sThisTemplate;
    }

    /**
     * Sends product suggestion mail and returns a URL according to
     * URL formatting rules.
     *
     * Template variables:
     * <b>editval</b>, <b>error</b>
     *
     * @return  null
     */
    public function send()
    {
        $aParams = oxConfig::getParameter( 'editval', true );
        if ( !is_array( $aParams ) ) {
            return;
        }

        // storing used written values
        $oParams = (object) $aParams;
        $this->setInviteData( (object) oxConfig::getParameter( 'editval' ) );

        // spam spider prevension
        $sMac     = oxConfig::getParameter( 'c_mac' );
        $sMacHash = oxConfig::getParameter( 'c_mach' );
        $oCaptcha = oxNew('oxCaptcha');

        if ( !$oCaptcha->pass($sMac, $sMacHash ) ) {
            // even if there is no exception, use this as a default display method
            oxUtilsView::getInstance()->addErrorToDisplay( 'EXCEPTION_INPUT_NOTALLFIELDS' );
            return;
        }

        $oUtilsView = oxUtilsView::getInstance();

        // filled not all fields ?
        foreach ( $this->_aReqFields as $sFieldName ) {
            //checking if any email was entered
            if ( $sFieldName == "rec_email" ) {
                foreach ( $aParams[$sFieldName] as $sKey => $sEmail ) {
                    //removing empty emails fields from eMails array
                    if ( empty( $sEmail ) ) {
                        unset( $aParams[$sFieldName][$sKey] );
                    }
                }

                //counting entered eMails
                if ( count( $aParams[$sFieldName] ) < 1 ) {
                    $oUtilsView->addErrorToDisplay('INVITE_COMLETECORRECTLYFIELDS');
                    return;
                }

                //updating values object
                $oParams->rec_email = $aParams[$sFieldName];
            }

            if ( !isset( $aParams[$sFieldName] ) || !$aParams[$sFieldName] ) {
                $oUtilsView->addErrorToDisplay('INVITE_COMLETECORRECTLYFIELDS');
                return;
            }
        }

        $oUtils = oxUtils::getInstance();

        //validating entered emails
        foreach ( $aParams["rec_email"] as $sRecipientEmail ) {
            if ( !$oUtils->isValidEmail( $sRecipientEmail ) ) {
                $oUtilsView->addErrorToDisplay('INVITE_INCORRECTEMAILADDRESS');
                return;
            }
        }

        if ( !$oUtils->isValidEmail( $aParams["send_email"] ) ) {
            $oUtilsView->addErrorToDisplay('INVITE_INCORRECTEMAILADDRESS');
            return;
        }

        // sending invite email
        $oEmail = oxNew( 'oxemail' );

        if ( $oEmail->sendInviteMail( $oParams ) ) {
            $this->_iMailStatus = 1;

            //getting active user
            $oUser = $this->getUser();

            //saving statitics for sended emails
            if ( $oUser ) {
                $this->_updateStatistics( $oUser->getId(), $aParams["rec_email"] );
            }

        } else {
            oxUtilsView::getInstance()->addErrorToDisplay('INVITE_ERRORWHILESENDINGMAIL');
        }
    }

    /**
     * Suggest data setter
     *
     * @param object $oData suggest data object
     *
     * @return null
     */
    public function setInviteData( $oData )
    {
        $this->_aInviteData = $oData;
    }

    /**
     * Template variable getter.
     *
     * @return array
     */
    public function getInviteData()
    {
        return $this->_aInviteData;
    }

    /**
     * Template variable getter. Returns object of handling CAPTCHA image
     *
     * @return object
     */
    public function getCaptcha()
    {
        if ( $this->_oCaptcha === null ) {
            $this->_oCaptcha = oxNew('oxCaptcha');
        }
        return $this->_oCaptcha;
    }

    /**
     * Updating invitations statistics
     *
     * @param string $sUserId   ID of user, who sents invitation
     * @param array  $aRecEmail array of recipients emails
     *
     * @return null
     */
    protected function _updateStatistics( $sUserId, $aRecEmail )
    {
        $oDb = oxDb::getDb( true );

        if ( $sUserId && is_array( $aRecEmail ) && count( $aRecEmail ) > 0 ) {
            //iserting statistics about invitation
            foreach ( $aRecEmail as $sRecEmail ) {
                $sSql = " INSERT INTO oxinvitations SET oxuserid = '$sUserId', oxemail = '$sRecEmail',  oxdate=NOW(), oxpending = '1', oxaccepted = '0', oxtype = '1' ";
                $oDb->execute( $sSql );
            }
        }
    }


}
