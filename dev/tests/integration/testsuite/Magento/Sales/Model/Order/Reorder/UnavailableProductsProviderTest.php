<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\Order\Reorder;

use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class UnavailableProductsProviderTest
 */
class UnavailableProductsProviderTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order_item_with_configurable_for_reorder.php
     */
    public function testGetForOrder()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var OrderFactory $orderFactory */
        $orderFactory = $objectManager->get(OrderInterfaceFactory::class);
        /** @var Order $order */
        $order = $orderFactory->create()->loadByIncrementId('100001001');
        $orderItems = $order->getItems();
        $orderItemSimple = array_pop($orderItems);
        $orderItemSimple->getSku();
        /** @var UnavailableProductsProvider $unavailableProductsProvider */
        $unavailableProductsProvider =
            $objectManager->create(UnavailableProductsProvider::class);
        $unavailableProducts = $unavailableProductsProvider->getForOrder($order);
        $this->assertEquals($orderItemSimple->getSku(), $unavailableProducts[0]);
    }
}
