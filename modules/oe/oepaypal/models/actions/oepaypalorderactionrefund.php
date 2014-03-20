<?php
/**
 * This file is part of OXID eSales PayPal module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eSales PayPal CE
 */

/**
 * PayPal order action refund class
 */
class oePayPalOrderActionRefund extends oePayPalOrderAction
{
    /**
     * Transaction id
     *
     * @var string
     */
    protected $_sTransactionId = null;

    /**
     * Amount to be refunded
     *
     * @var float
     */
    protected $_dAmount = null;

    /**
     * Refund type: Partial|Full
     *
     * @var string
     */
    protected $_sType = null;

    /**
     * PayPal Request
     *
     * @var oePayPalRequest
     */
    protected $_oPayPalRequest = null;

    /**
     * PayPal Response
     *
     * @var oePayPalRequest
     */
    protected $_oPayPalResponse = null;

    /**
     * Payment that should be refunded
     *
     * @var oePayPalOrderPayment
     */
    protected $_oPaymentBeingRefunded = null;

    /**
     * Processes PayPal response
     */
    public function process()
    {
        $oResponse = $this->getPayPalResponse();

        $oOrder = $this->getOrder();
        $oOrder->addRefundedAmount( $oResponse->getRefundAmount() );
        $this->_changeOrderStatus();
        $oOrder->save();

        $oPayment = oxNew( 'oePayPalOrderPayment' );
        $oPayment->setDate($this->getDate());
        $oPayment->setTransactionId($oResponse->getTransactionId());
        $oPayment->setCorrelationId( $oResponse->getCorrelationId() );
        $oPayment->setAction('refund');
        $oPayment->setStatus( $oResponse->getPaymentStatus() );
        $oPayment->setAmount( $oResponse->getRefundAmount() );
        $oPayment->setCurrency( $oResponse->getCurrency() );

        $oRefundedPayment = $this->getPaymentBeingRefunded();
        $oRefundedPayment->addRefundedAmount( $oResponse->getRefundAmount() );
        $oRefundedPayment->save();

        $oPayment = $oOrder->getPaymentList()->addPayment( $oPayment );

        if ( $this->getComment() ) {
            $oComment = oxNew('oePayPalOrderPaymentComment');
            $oComment->setComment( $this->getComment() );
            $oPayment->addComment( $oComment );
        }
    }

    /**
     * Returns PayPal response; initiates if not set
     *
     * @return mixed
     */
    public function getPayPalResponse()
    {
        if ( is_null( $this->_oPayPalResponse ) ) {
            $oService = $this->getPayPalService();
            $oRequest = $this->getPayPalRequest();
            $this->_oPayPalResponse = $oService->refundTransaction( $oRequest );
        }
        return $this->_oPayPalResponse;
    }

    /**
     * Sets PayPal response
     *
     * @param oePayPalResponseDoRefund $oPayPalResponse
     */
    public function setPayPalResponse( $oPayPalResponse )
    {
        $this->_oPayPalResponse = $oPayPalResponse;
    }

    /**
     * Returns PayPal request; initiates if not set
     *
     * @return oePayPalPayPalRequest
     */
    public function getPayPalRequest()
    {
        if ( is_null( $this->_oPayPalRequest ) ) {
            $oRequestBuilder = $this->getPayPalRequestBuilder();

            $oRequestBuilder->setTransactionId( $this->getTransactionId() );
            $oRequestBuilder->setAmount( $this->getAmount(), $this->getOrder()->getCurrency() );
            $oRequestBuilder->setRefundType( $this->getType() );
            $oRequestBuilder->setComment( $this->getComment() );

            $this->_oPayPalRequest = $oRequestBuilder->getRequest();
        }

        return $this->_oPayPalRequest;
    }

    /**
     * Sets PayPal request
     *
     * @param $oPayPalRequest
     */
    public function setPayPalRequest( $oPayPalRequest )
    {
        $this->_oPayPalRequest = $oPayPalRequest;
    }

    /**
     * Returns amount to refund
     *
     * @return float
     */
    public function getAmount()
    {
        if ( is_null( $this->_dAmount ) ) {
            $dAmount = $this->getRequest()->getRequestParameter( 'refund_amount' );
            $this->_dAmount = $dAmount? $dAmount : $this->getPaymentBeingRefunded()->getRemainingRefundAmount();
        }
        return $this->_dAmount;
    }

    /**
     * Sets amount to refund
     *
     * @param float $dAmount
     */
    public function setAmount( $dAmount )
    {
        $this->_dAmount = $dAmount;
    }

    /**
     * Returns payment to refund
     *
     * @return float
     */
    public function getPaymentBeingRefunded()
    {
        if ( is_null( $this->_oPaymentBeingRefunded ) ) {
            $this->_oPaymentBeingRefunded = oxNew("oePayPalOrderPayment");
            $this->_oPaymentBeingRefunded->loadByTransactionId( $this->getTransactionId() );
        }
        return $this->_oPaymentBeingRefunded;
    }

    /**
     * Sets payment to refund
     *
     * @param float $dAmount
     */
    public function setPaymentBeingRefunded( $dAmount )
    {
        $this->_oPaymentBeingRefunded = $dAmount;
    }

    /**
     * Returns refund type; gets from request if not set
     *
     * @return string
     */
    public function getType()
    {
        if ( is_null( $this->_sType ) ) {
            $this->_sType = $this->getRequest()->getRequestParameter( 'refund_type' );
        }
        return $this->_sType;
    }

    /**
     * Sets refund type
     *
     * @param string $sType
     */
    public function setType( $sType )
    {
        $this->_sType = $sType;
    }

    /**
     * Return transaction id
     *
     * @return string
     */
    public function getTransactionId()
    {
        if ( is_null( $this->_sTransactionId ) ) {
            $this->_sTransactionId = $this->getRequest()->getRequestParameter( 'transaction_id' );
        }
        return $this->_sTransactionId;
    }

    /**
     * Sets transaction id
     *
     * @param string $sTransactionId
     */
    public function setTransactionId( $sTransactionId )
    {
        $this->_sTransactionId = $sTransactionId;
    }
}