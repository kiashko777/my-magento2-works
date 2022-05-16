<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Downloadable\Model\Observer;

use Magento\Downloadable\Model\Link\Purchased\Item;
use Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\Collection;
use Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\CollectionFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for case, when customer is able to download
 * downloadable product, after order was canceled.
 */
class SetLinkStatusObserverTest extends TestCase
{
    /**
     * Object manager
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Asserting, that links status is expired after canceling of order.
     * This test relates to the GitHub issue magento/magento2#8515.
     *
     * @magentoDataFixture Magento/Downloadable/_files/product_downloadable.php
     * @magentoDataFixture Magento/Downloadable/_files/order_with_downloadable_product.php
     * @magentoDbIsolation disabled
     */
    public function testCheckStatusOnOrderCancel()
    {
        /** @var Order $order */
        $order = $this->objectManager->create(Order::class);
        $order->loadByIncrementId('100000001');

        $orderItems = $order->getAllItems();
        $items = array_values($orderItems);
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = array_shift($items);

        /** Canceling order to reproduce test case */
        $order->setState(Order::STATE_CANCELED);
        $order->save();

        /** @var Collection $linkCollection */
        $linkCollection = $this->objectManager->create(
            CollectionFactory::class
        )->create();

        $linkCollection->addFieldToFilter('order_item_id', $orderItem->getId());

        /** Assert there are items in linkCollection to avoid false-positive test result. */
        $this->assertGreaterThan(0, $linkCollection->count());

        /** @var Item $linkItem */
        foreach ($linkCollection->getItems() as $linkItem) {
            $this->assertEquals(
                Item::LINK_STATUS_EXPIRED,
                $linkItem->getStatus()
            );
        }
    }

    /**
     * Initialization of dependencies
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
