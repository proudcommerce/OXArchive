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
 * PayPal order action capture class
 */
class oePayPalOrderActionCapture extends oePayPalOrderAction
{
    /**
     * Amount to be captured
     *
     * @var double
     */
    protected $_dAmount = null;

    /**
     * Capture type: Complete|NotComplete
     *
     * @var string
     */
    protected $_sType = null;

    /**
     * PayPal request
     *
     * @var oePayPalRequest
     */
    protected $_oPayPalRequest = null;

    /**
     * PayPal response
     *
     * @var oePayPalRequest
     */
    protected $_oPayPalResponse = null;

    /**
     * Processes PayPal response
     */
    public function process()
    {
        $oResponse = $this->getPayPalResponse();

        $oOrder = $this->getOrder();
        $oOrder->addCapturedAmount( $oResponse->getCapturedAmount() );
        $this->_changeOrderStatus();
        $oOrder->save();

        $oPayment = oxNew( 'oePayPalOrderPayment' );
        $oPayment->setDate($this->getDate());
        $oPayment->setTransactionId($oResponse->getTransactionId());
        $oPayment->setCorrelationId( $oResponse->getCorrelationId() );
        $oPayment->setAction('capture');
        $oPayment->setStatus( $oResponse->getPaymentStatus() );
        $oPayment->setAmount( $oResponse->getCapturedAmount() );
        $oPayment->setCurrency( $oResponse->getCurrency() );

        $oPayment = $oOrder->getPaymentList()->addPayment( $oPayment );
        if ( $this->getComment() ) {
            $oComment = oxNew('oePayPalOrderPaymentComment');
            $oComment->setComment( $this->getComment() );
            $oPayment->addComment( $oComment );
        }
    }

    /**
     * Returns PayPal response; calls PayPal if not set
     *
     * @return mixed
     */
    public function getPayPalResponse()
    {
        if ( is_null( $this->_oPayPalResponse ) ) {
            $oService = $this->getPayPalService();
            $oRequest = $this->getPayPalRequest();
            $this->_oPayPalResponse = $oService->doCapture( $oRequest );
        }
        return $this->_oPayPalResponse;
    }

    /**
     * Set PayPal response
     *
     * @param oePayPalResponseDoRefund $oPayPalResponse
     */
    public function setPayPalResponse( $oPayPalResponse )
    {
        $this->_oPayPalResponse = $oPayPalResponse;
    }

    /**
     * Returns PayPal request; initializes if not set
     *
     * @return oePayPalPayPalRequest
     */
    public function getPayPalRequest()
    {
        if ( is_null( $this->_oPayPalRequest ) ) {
            $oRequestBuilder = $this->getPayPalRequestBuilder();

            $oRequestBuilder->setAuthorizationId( $this->getAuthorizationId() );
            $oRequestBuilder->setAmount( $this->getAmount(), $this->getOrder()->getCurrency() );
            $oRequestBuilder->setCompleteType( $this->getType() );
            $oRequestBuilder->setComment( $this->getComment() );

            $this->_oPayPalRequest = $oRequestBuilder->getRequest();
        }

        return $this->_oPayPalRequest;
    }

    /**
     * Set PayPal request
     *
     * @param oePayPalPayPalRequest $oPayPalRequest
     */
    public function setPayPalRequest( $oPayPalRequest )
    {
        $this->_oPayPalRequest = $oPayPalRequest;
    }

    /**
     * Returns captured amount; Takes from request if not set
     *
     * @return double
     */
    public function getAmount()
    {
        if ( is_null( $this->_dAmount ) ) {
            $dAmount = $this->getRequest()->getRequestParameter( 'capture_amount' );
            $this->_dAmount = $dAmount? $dAmount : $this->getOrder()->getRemainingOrderSum();
        }
        return $this->_dAmount;
    }

    /**
     * Sets captured amount
     *
     * @param double $dAmount
     */
    public function setAmount( $dAmount )
    {
        $this->_dAmount = $dAmount;
    }

    /**
     * Returns capture type; Takes from request if not set
     *
     * @return string
     */
    public function getType()
    {
        if ( is_null( $this->_sType ) ) {
            $this->_sType = $this->getRequest()->getRequestParameter( 'capture_type' );
        }
        return $this->_sType;
    }

    /**
     * Sets captured type
     *
     * @param string $sType
     */
    public function setType( $sType )
    {
        $this->_sType = $sType;
    }
}