<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Controller;

use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * @magentoDataFixture Magento/Sales/_files/order.php
 */
class HostedproTest extends AbstractController
{
    /**
     * @magentoDataFixture Magento/Paypal/_files/quote_payment_express.php
     * @magentoConfigFixture current_store payment/paypal_hostedpro/active 1
     * @magentoConfigFixture current_store paypal/general/business_account merchant_2012050718_biz@example.com
     */
    public function testCancelAction()
    {
        $order = $this->_objectManager->create(Order::class);
        $session = $this->_objectManager->get(Session::class);

        $quote = $this->_objectManager->create(Quote::class);
        $quote->load('100000002', 'reserved_order_id');
        $session->setQuoteId($quote->getId());
        $session->setPaypalStandardQuoteId($quote->getId())->setLastRealOrderId('100000002');
        $this->dispatch('paypal/hostedpro/cancel');

        $order->load('100000002', 'increment_id');
        $this->assertEquals('canceled', $order->getState());
        $this->assertEquals($session->getQuote()->getGrandTotal(), $quote->getGrandTotal());
        $this->assertEquals($session->getQuote()->getItemsCount(), $quote->getItemsCount());
    }
}
