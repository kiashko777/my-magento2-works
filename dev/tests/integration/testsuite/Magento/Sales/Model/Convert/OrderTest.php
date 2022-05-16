<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\Convert;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class OrderTest
 */
class OrderTest extends TestCase
{
    /** @var Order */
    protected $_model;

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testConvertToCreditmemo()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order::class);
        $order->loadByIncrementId('100000001');
        //MAGETWO-45612 fix
        $order->setBaseShippingAmount(5);
        $this->assertNull($this->_model->toCreditmemo($order)->getBaseShippingAmount());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(Order::class);
    }
}
