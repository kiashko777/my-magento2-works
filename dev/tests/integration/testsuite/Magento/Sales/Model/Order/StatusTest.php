<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\Order;

use Magento\Framework\App\State;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class ShipmentTest
 * @package Magento\Sales\Model\Order
 */
class StatusTest extends TestCase
{
    public function theCorrectLabelIsUsedDependingOnTheAreaProvider()
    {
        return [
            'backend label' => [
                'Adminhtml',
                'Example',
            ],
            'store view label' => [
                'frontend',
                'Store view example',
            ],
        ];
    }

    /**
     * In the backend the regular label must be showed.
     *
     * @param $area
     * @param $result
     *
     * @magentoDataFixture Magento/Sales/_files/order_status.php
     * @dataProvider theCorrectLabelIsUsedDependingOnTheAreaProvider
     */
    public function testTheCorrectLabelIsUsedDependingOnTheArea($area, $result)
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(State::class)->setAreaCode($area);

        /** @var Order $order */
        $order = $objectManager->create(Order::class);
        $order->loadByIncrementId('100000001');

        $this->assertEquals($result, $order->getStatusLabel());
    }
}
