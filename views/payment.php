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
 * $Id: payment.php 22336 2009-09-15 15:44:43Z vilma $
 */

/**
 * Includes credit card validation class.
 */
require_once oxConfig::getInstance()->getConfigParam( 'sCoreDir' ) . "ccval/ccval.php";

/**
 * Payment manager.
 * Customer payment manager class. Performs payment validation function, etc.
 * @package main
 */
class Payment extends oxUBase
{
    /**
     * Paymentlist
     * @var object
     */
    protected $_oPaymentList = null;

    /**
     * Paymentlist count
     * @var integer
     */
    protected $_iPaymentCnt = null;

    /**
     * All delivery sets
     * @var array
     */
    protected $_aAllSets = null;

    /**
     * Delivery sets count
     * @var integer
     */
    protected $_iAllSetsCnt = null;

    /**
     * Payment object 'oxempty'
     * @var object
     */
    protected $_oEmptyPayment = null;

    /**
     * Payment error
     * @var string
     */
    protected $_sPaymentError = null;

    /**
     * Payment error text
     * @var string
     */
    protected $_sPaymentErrorText = null;

    /**
     * Dyn values
     * @var array
     */
    protected $_aDynValue = null;

    /**
     * Checked payment id
     * @var string
     */
    protected $_sCheckedId = null;

    /**
     * Selected payment id in db
     * @var string
     */
    protected $_sCheckedPaymentId = null;

    /**
     * array of years
     * @var array
     */
    protected $_aCreditYears = null;

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'payment.tpl';

    /**
     * Order step marker
     * @var bool
     */
    protected $_blIsOrderStep = true;

    /**
     * Executes parent method parent::init().
     *
     * @return null
     */
    public function init()
    {
        parent::init();

        if ( ( $soxAddressId = oxConfig::getParameter( 'oxaddressid' ) ) ) {
            oxSession::setVar( 'deladdrid', $soxAddressId );
        }
    }

    /**
     * Executes parent::render(), checks if this connection secure
     * (if not - redirects to secure payment page), loads user object
     * (if user object loading was not successfull - redirects to start
     * page), loads user delivery/shipping information. According
     * to configuration in admin, user profile data loads delivery sets,
     * and possible payment methods. Returns name of template to render
     * payment::_sThisTemplate.
     *
     * Template variables:
     * <b>allShipsetsCnt</b>, <b>allShipsets</b>, <b>payments</b>,
     * <b>payerror</b>, <b>payerrortext</b>, <b>dynvalue</b>,
     * <b>defpaymentid</b>, <b>basket</b>, <b>deladr</b>
     *
     * @return  string  current template file name
     */
    public function render()
    {
        parent::render();

        $myConfig  = $this->getConfig();

        //if it happens that you are not in SSL
        //then forcing to HTTPS

        //but first checking maybe there were redirection already to prevent infinite redirections due to possible buggy ssl detection on server
        $blAlreadyRedirected = oxConfig::getParameter( 'sslredirect' ) == 'forced';

        if ( $myConfig->getCurrentShopURL() != $myConfig->getSSLShopURL() && !$blAlreadyRedirected && !oxConfig::getParameter('fnc') ) {
            $sPayError = oxConfig::getParameter( 'payerror' )?'payerror='.oxConfig::getParameter( 'payerror' ):'';
            $sPayErrorText = oxConfig::getParameter('payerrortext')?'payerrortext='.oxConfig::getParameter( 'payerrortext' ):'';
            $sRedirectURL = $myConfig->getShopSecureHomeURL().'sslredirect=forced&cl=payment&'.$sPayError."&".$sPayErrorText;
            oxUtils::getInstance()->redirect( $sRedirectURL );
        }

        //additional check if we really really have a user now
        //and the basket is not empty
        $oUser = $this->getUser();
        $oBasket = $this->getSession()->getBasket();
        if ( !$oBasket || !$oUser || ( $oBasket && !$oBasket->getProductsCount() ) ) {
            oxUtils::getInstance()->redirect( $myConfig->getShopHomeURL() );
        }

        // passing payments to view
        $this->_aViewData[ 'payments' ] = $this->getPaymentList();

        // #955A. must recalculate count
        $this->_aViewData['allShipsetsCnt'] = $this->getAllSetsCnt();
        $this->_aViewData['allShipsets']    = $this->getAllSets();

        if ( !$this->getAllSetsCnt() ) {
            // no fitting shipping set found, setting default empty payment
            $this->_setDefaultEmptyPayment();
            oxSession::setVar( 'sShipSet', null );
        }

        $this->_unsetPaymentErrors();

        $this->_aViewData['oxemptypayment'] = $this->getEmptyPayment();
        $this->_aViewData['payerror']       = $this->getPaymentError();
        $this->_aViewData['payerrortext']   = $this->getPaymentErrorText();

        $this->_aViewData['dynvalue']  = $this->getDynValue();

        // get checked payment ID
        $this->_aViewData['defpaymentid'] = $this->getCheckedPaymentId();
        $this->_aViewData['paymencnt']    = $this->getPaymentCnt();

        //add a array with current years for the credit card drop down box
        $this->_aViewData['creditYears'] = $this->getCreditYears();

        return $this->_sThisTemplate;
    }

    /**
     * Set default empty payment. If config param 'blOtherCountryOrder' is on,
     * tries to set 'oxempty' payment to aViewData['oxemptypayment'].
     * On error sets aViewData['payerror'] to -2
     *
     * @return null
     */
    protected function _setDefaultEmptyPayment()
    {
        // no shipping method there !!
        if ( $this->getConfig()->getConfigParam( 'blOtherCountryOrder' ) ) {
            $oPayment = oxNew( 'oxpayment' );
            if ( $oPayment->load( 'oxempty' ) ) {
                $this->_oEmptyPayment = $oPayment;
            } else { // some error with setup ??
                $this->_sPaymentError = -2;
            }
        } else {
            $this->_sPaymentError = -2;
        }
    }

    /**
     * Unsets payment errors from session
     *
     * @return null
     */
    protected function _unsetPaymentErrors()
    {
        $iPayError     = oxConfig::getParameter( 'payerror' );
        $iPayErrorText = oxConfig::getParameter( 'payerrortext' );

        if ( $iPayError ) {
            oxSession::deleteVar( 'payerror' );
            $this->_sPaymentError = $iPayError;
            //QWERTY
        }
        if ( $iPayErrorText ) {
            oxSession::deleteVar( 'payerrortext' );
            $this->_sPaymentErrorText = $iPayErrorText;
            //QWERTY
        }
    }

    /**
     * Changes shipping set to chosen one. Sets basket status to not up-to-date, which later
     * forces to recalculate it
     *
     * @return null
     */
    public function changeshipping()
    {
        $mySession = $this->getSession();

        oxSession::setVar( 'sShipSet', oxConfig::getParameter( 'sShipSet' ) );
        $oBasket = $mySession->getBasket();
        $oBasket->setShipping( null );
        $oBasket->onUpdate();
    }

    /**
     * Validates oxidcreditcard and oxiddebitnote user payment data.
     * Returns false if problems on validating occured. If everything
     * is OK - returns "order" and redirects to payment confirmation
     * page.
     *
     * Session variables:
     * <b>paymentid</b>, <b>dynvalue</b>, <b>payerror</b>
     *
     * @todo    this function is called in templates, sure to be protected ?
     *
     * @return  mixed
     */
    public function validatePayment()
    {
        $myConfig  = $this->getConfig();
        $mySession = $this->getSession();

        //#1308C - check user. Function is executed before render(), and oUser is not set!
        // Set it manually for use in methods getPaymentList(), getShippingSetList()...
        $oUser = $this->getUser();
        if ( !$oUser ) {
            oxSession::setVar( 'payerror', 2 );
            return;
        }

        if (! ($sShipSetId = oxConfig::getParameter( 'sShipSet' ))) {
            $sShipSetId = oxSession::getVar('sShipSet');
        }
        $sPaymentId = oxConfig::getParameter( 'paymentid' );
        $aDynvalue  = oxConfig::getParameter( 'dynvalue' );

        // A. additional protection
        if ( !$myConfig->getConfigParam( 'blOtherCountryOrder' ) && $sPaymentId == 'oxempty' ) {
            $sPaymentId = '';
        }

        //#1308C - check if we have paymentID, and it really exists
        if ( !$sPaymentId ) {
            oxSession::setVar( 'payerror', 1 );
            return;
        }

        $oBasket = $mySession->getBasket();
        $oBasket->setPayment(null);
        $oPayment = oxNew( 'oxpayment' );
        $oPayment->load( $sPaymentId );

        // getting basket price for payment calculation
        $dBasketPrice = $oBasket->getPriceForPayment();

        $blOK = $oPayment->isValidPayment( $aDynvalue, $myConfig->getShopId(), $oUser, $dBasketPrice, $sShipSetId );

        if ( $blOK ) {
            oxSession::setVar( 'paymentid', $sPaymentId );
            oxSession::setVar( 'dynvalue', $aDynvalue );
            oxSession::deleteVar( '_selected_paymentid' );
            return 'order';
        } else {
            oxSession::setVar( 'payerror', 1 );

            //#1308C - delete paymentid from session, and save selected it just for view
            oxSession::deleteVar( 'paymentid' );
            oxSession::setVar( '_selected_paymentid', $sPaymentId );
            return;
        }
    }

    /**
     * Template variable getter. Returns paymentlist
     *
     * @return object
     */
    public function getPaymentList()
    {
        if ( $this->_oPaymentList === null ) {
            $this->_oPaymentList = false;

            $sActShipSet = oxConfig::getParameter( 'sShipSet' );
            $oBasket = $this->getSession()->getBasket();

            // load sets, active set, and active set payment list
            list( $aAllSets, $sActShipSet, $aPaymentList ) = oxDeliverySetList::getInstance()->getDeliverySetData( $sActShipSet, $this->getUser(), $oBasket );

            oxSession::setVar( 'sShipSet', $sActShipSet );
            // calculating payment expences for preview for each payment
            $this->_setDeprecatedValues( $aPaymentList, $oBasket );
            $this->_oPaymentList = $aPaymentList;
            $this->_aAllSets     = $aAllSets;

        }
        return $this->_oPaymentList;
    }

    /**
     * Template variable getter. Returns all delivery sets
     *
     * @return array
     */
    public function getAllSets()
    {
        if ( $this->_aAllSets === null ) {
            $this->_aAllSets = false;

            if ($this->getPaymentList()) {
                return $this->_aAllSets;
            }
        }
        return $this->_aAllSets;
    }

    /**
     * Template variable getter. Returns number of delivery sets
     *
     * @return integer
     */
    public function getAllSetsCnt()
    {
        if ( $this->_iAllSetsCnt === null ) {
            $this->_iAllSetsCnt = 0;

            if ($this->getPaymentList()) {
                $this->_iAllSetsCnt = count($this->_aAllSets);
            }
        }
        return $this->_iAllSetsCnt;
    }

    /**
     * Calculate payment cost for each payment. Sould be removed later
     *
     * @param array    &$aPaymentList payments array
     * @param oxBasket $oBasket       basket object
     *
     * @return null
     */
    protected function _setDeprecatedValues( & $aPaymentList, $oBasket = null )
    {
        if ( is_array($aPaymentList) ) {
            $oLang = oxLang::getInstance();
            foreach ( $aPaymentList as $oPayment ) {
                $oPrice = $oPayment->getPaymentPrice( $oBasket );
                $oPayment->dAddPaymentSum = $oPrice->getBruttoPrice();
                $oPayment->fAddPaymentSum = $oLang->formatCurrency( $oPayment->dAddPaymentSum, $oBasket->getBasketCurrency() );
                $oPayment->aDynValues     = $oPayment->getDynValues();
                if ( $oPayment->oxpayments__oxchecked->value ) {
                    $this->_sCheckedId = $oPayment->getId();
                }
            }
        }
    }

    /**
     * Template variable getter. Returns payment object "oxempty"
     *
     * @return object
     */
    public function getEmptyPayment()
    {
        return $this->_oEmptyPayment;
    }

    /**
     * Template variable getter. Returns error of payments
     *
     * @return string
     */
    public function getPaymentError()
    {
        return $this->_sPaymentError;
    }

    /**
     * Template variable getter. Returns error text of payments
     *
     * @return string
     */
    public function getPaymentErrorText()
    {
        return $this->_sPaymentErrorText;
    }

    /**
     * Template variable getter. Returns dyn values
     *
     * @return array
     */
    public function getDynValue()
    {
        if ( $this->_aDynValue === null ) {
            $this->_aDynValue = false;

            // #1217 R
            if ( ( $aDynValue = oxSession::getVar( 'dynvalue' ) ) ) {
                $this->_aDynValue  = $aDynValue;
            } else {
                $this->_aDynValue  = oxConfig::getParameter( "dynvalue");
            }

            // #701A
            // assign debit note payment params to view data
            $aPaymentList = $this->getPaymentList();
            if ( isset( $aPaymentList['oxiddebitnote'] ) ) {
                $this->_assignDebitNoteParams();
            }
        }
        return $this->_aDynValue;
    }

    /**
     * Assign debit note payment values to view data. Loads user debit note payment
     * if available and assigns payment data to $this->_aDynValue
     *
     * @return null
     */
    protected function _assignDebitNoteParams()
    {
        // #701A
        $oUserPayment = oxNew( 'oxuserpayment');
        //such info available ?
        if ( $oUserPayment->getPaymentByPaymentType( $this->getUser(), 'oxiddebitnote' ) ) {
            $aAddPaymentData = oxUtils::getInstance()->assignValuesFromText( $oUserPayment->oxuserpayments__oxvalue->value );

            //checking if some of values is allready set in session - leave it
            foreach ( $aAddPaymentData as $oData ) {
                if ( !isset( $this->_aDynValue[$oData->name] ) ||
                   (  isset( $this->_aDynValue[$oData->name] ) && !$this->_aDynValue[$oData->name] ) ) {
                    $this->_aDynValue[$oData->name] = $oData->value;
                }
            }
        }
    }

    /**
     * Get checked payment ID. Tries to get checked payment ID from session,
     * if fails, then tries to get payment ID from last order.
     *
     * @return string
     */
    public function getCheckedPaymentId()
    {
        if ( $this->_sCheckedPaymentId === null ) {
            if ( ( $sPaymentID = oxConfig::getParameter( 'paymentid' ) ) ) {
                $sCheckedId = $sPaymentID;
            } elseif ( ( $sSelectedPaymentID = oxSession::getVar( '_selected_paymentid' ) ) ) {
                $sCheckedId = $sSelectedPaymentID;
            } else {
                // #1010A.
                if ( $oUser = $this->getUser()) {
                    $oOrder = oxNew('oxorder');
                    if ( ( $sLastPaymentId = $oOrder->getLastUserPaymentType( $oUser->getId()) ) ) {
                        $sCheckedId = $sLastPaymentId;
                    }
                }
            }

            // #M253 set to selected payment in db
            if ( !$sCheckedId && $this->_sCheckedId ) {
                $sCheckedId = $this->_sCheckedId;
            }

            // #646
            $oPaymentList = $this->getPaymentList();
            if ( isset( $oPaymentList ) && $oPaymentList && !isset( $oPaymentList[$sCheckedId] ) ) {
                end($oPaymentList);
                $sCheckedId = key( $oPaymentList );
            }
            $this->_sCheckedPaymentId = $sCheckedId;
        }

        return $this->_sCheckedPaymentId;
    }

    /**
     * Template variable getter. Returns payment list count
     *
     * @return integer
     */
    public function getPaymentCnt()
    {
        if ( $this->_iPaymentCnt === null ) {
            $this->_iPaymentCnt = false;

            if ($oPaymentList = $this->getPaymentList()) {
                $this->_iPaymentCnt = count($oPaymentList);
            }
        }
        return $this->_iPaymentCnt;
    }

    /**
     * Template variable getter. Returns array of years for credit cards
     *
     * @return array
     */
    public function getCreditYears()
    {
        if ( $this->_aCreditYears === null ) {
            $this->_aCreditYears = false;

            $this->_aCreditYears = range( date('Y'), date('Y') + 10 );
        }
        return $this->_aCreditYears;
    }

}
