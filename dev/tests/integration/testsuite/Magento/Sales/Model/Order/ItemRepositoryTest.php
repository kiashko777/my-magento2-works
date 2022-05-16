<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Model\Order;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ItemRepositoryTest extends TestCase
{
    /** @var Order */
    private $order;

    /** @var OrderItemRepositoryInterface */
    private $orderItemRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * @magentoDataFixture Magento/Sales/_files/order_configurable_product.php
     */
    public function testAddOrderItemParent()
    {
        $this->order->load('100000001', 'increment_id');

        foreach ($this->order->getItems() as $item) {
            if ($item->getProductType() === Type::TYPE_SIMPLE) {
                $orderItem = $this->orderItemRepository->get($item->getItemId());
                $this->assertInstanceOf(OrderItemInterface::class, $orderItem->getParentItem());
            }
        }

        $itemList = $this->orderItemRepository->getList(
            $this->searchCriteriaBuilder->addFilter('order_id', $this->order->getId())->create()
        );

        foreach ($itemList->getItems() as $item) {
            if ($item->getProductType() === Type::TYPE_SIMPLE) {
                $this->assertInstanceOf(OrderItemInterface::class, $item->getParentItem());
            }
        }
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $this->order = $objectManager->create(Order::class);
        $this->orderItemRepository = $objectManager->create(OrderItemRepositoryInterface::class);
        $this->searchCriteriaBuilder = $objectManager->create(SearchCriteriaBuilder::class);
    }
}
