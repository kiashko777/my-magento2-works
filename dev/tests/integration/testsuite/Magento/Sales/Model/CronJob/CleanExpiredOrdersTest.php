<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\CronJob;

use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CleanExpiredOrdersTest extends TestCase
{
    /**
     * @magentoConfigFixture default sales/orders/delete_pending_after 0
     * @magentoConfigFixture current_store sales/orders/delete_pending_after 0
     * @magentoDataFixture Magento/Sales/_files/order_pending_payment.php
     */
    public function testExecute()
    {
        /** @var CleanExpiredOrders $job */
        $job = Bootstrap::getObjectManager()->create(CleanExpiredOrders::class);
        $job->execute();

        /** @var Order $order */
        $order = Bootstrap::getObjectManager()->create(Order::class);
        $order->load('100000001', 'increment_id');
        $this->assertEquals(Order::STATE_CANCELED, $order->getStatus());
    }
}
