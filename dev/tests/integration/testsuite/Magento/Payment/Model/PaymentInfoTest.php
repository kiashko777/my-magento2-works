<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Payment\Model;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Payment;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PaymentInfoTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Order
     */
    protected $_order;

    /** @var Quote */
    protected $_quote;

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Payment/_files/payment_info.php
     */
    public function testUnsetPaymentInformation()
    {
        $order = $this->_order->loadByIncrementId('100000001');
        /** @var \Magento\Sales\Model\Order\Payment $paymentOrder */
        $paymentOrder = $order->getPayment();
        $paymentOrder->unsAdditionalInformation('testing');

        $quote = $this->_quote->load('reserved_order_id', 'reserved_order_id');
        /** @var Payment $paymentQuote */
        $paymentQuote = $quote->getPayment();
        $paymentQuote->unsAdditionalInformation('testing');

        $this->assertFalse($paymentOrder->hasAdditionalInformation('testing'));
        $this->assertFalse($paymentQuote->hasAdditionalInformation('testing'));
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_order = $this->_objectManager->create(
            Order::class
        );
        $this->_quote = $this->_objectManager->create(
            Quote::class
        );
    }
}
