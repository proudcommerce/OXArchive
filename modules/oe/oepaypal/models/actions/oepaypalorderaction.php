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
 * PayPal order action class
 */
abstract class oePayPalOrderAction
{
    /**
     * Action name
     *
     * @var string
     */
    protected $_sActionName = null;

    /**
     * Request object
     *
     * @var oePayPalRequest
     */
    protected $_oRequest = null;

    /**
     *
     *
     * @var oePayPalRequest
     */
    protected $_oOrder = null;

    /**
     * @var string
     */
    protected $_sOrderStatus = null;

    /**
     * @var string
     */
    protected $_sAuthorizationId = null;

    /**
     * @var string
     */
    protected $_sComment = null;

    /**
     * @var oePayPalService
     */
    protected $_oPayPalService = null;

    /**
     * PayPal order
     *
     * @var oePayPalPayPalOrder
     */
    protected $_oPayPalRequestBuilder = null;

    /**
     * Sets request object
     *
     * @param oePayPalRequest $oRequest
     */
    public function setRequest( $oRequest )
    {
        $this->_oRequest = $oRequest;
    }

    /**
     * Returns request object
     *
     * @return oePayPalRequest
     */
    public function getRequest()
    {
        return $this->_oRequest;
    }

    /**
     * Sets order object
     *
     * @param oePayPalPayPalOrder $oOrder
     */
    public function setOrder( $oOrder )
    {
        $this->_oOrder = $oOrder;
    }

    /**
     * Returns order object
     *
     * @return oePayPalPayPalOrder
     */
    public function getOrder()
    {
        return $this->_oOrder;
    }

    /**
     * Sets order status
     *
     * @param string $sOrderStatus
     */
    public function setOrderStatus( $sOrderStatus )
    {
        $this->_sOrderStatus = $sOrderStatus;
    }

    /**
     * Returns order status
     *
     * @return string
     */
    public function getOrderStatus()
    {
        if ( $this->_sOrderStatus === null ) {
            $sStatus = $this->getRequest()->getRequestParameter( 'order_status' );
            $this->setOrderStatus( $sStatus );
        }
        return $this->_sOrderStatus;
    }

    /**
     * Sets order status to PayPal order
     */
    protected function _changeOrderStatus()
    {
        $sStatus = $this->getOrderStatus();
        $this->getOrder()->setPaymentStatus( $sStatus );
    }

    /**
     * Returns authorization id
     *
     * @return string
     */
    public function getAuthorizationId()
    {
        return $this->_sAuthorizationId;
    }

    /**
     * Sets authorization id
     *
     * @param string $sAuthorizationId
     */
    public function setAuthorizationId( $sAuthorizationId )
    {
        $this->_sAuthorizationId = $sAuthorizationId;
    }

    /**
     * Sets PayPal request builder
     *
     * @param oePayPalPayPalRequestBuilder $oBuilder
     */
    public function setPayPalRequestBuilder( $oBuilder )
    {
        $this->_oPayPalRequestBuilder = $oBuilder;
    }

    /**
     * Returns PayPal request builder
     *
     * @return oePayPalPayPalRequestBuilder
     */
    public function getPayPalRequestBuilder()
    {
        if ( $this->_oPayPalRequestBuilder === null ) {
            $this->_oPayPalRequestBuilder = oxNew( 'oePayPalPayPalRequestBuilder' );
        }
        return $this->_oPayPalRequestBuilder;
    }

    /**
     * Sets PayPal service
     *
     * @param oePayPalService $oService
     */
    public function setPayPalService( $oService )
    {
        $this->_oPayPalService = $oService;
    }

    /**
     * Returns PayPal service
     *
     * @return oePayPalService
     */
    public function getPayPalService()
    {
        if ( $this->_oPayPalService === null ) {
            $this->_oPayPalService = oxNew( 'oePayPalService' );
        }
        return $this->_oPayPalService;
    }

    /**
     * Returns formatted date
     *
     * @return string
     */
    public function getDate()
    {
        return date( 'Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime() );
    }

    /**
     * Sets action comment
     *
     * @param string $sComment
     */
    public function setComment( $sComment )
    {
        $this->_sComment = $sComment;
    }

    /**
     * Returns action comment
     *
     * @return string
     */
    public function getComment()
    {
        if ( is_null( $this->_sComment ) ) {
            $sComment = $this->getRequest()->getRequestParameter( 'action_comment' );
            $this->setComment( $sComment );
        }
        return $this->_sComment;
    }

    /**
     * Processes PayPal action
     */
    abstract public function process();
}