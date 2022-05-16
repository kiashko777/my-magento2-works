<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Model\Order;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Invoice model test.
 */
class InvoiceTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var OrderCollection
     */
    private $collection;

    /**
     * @var InvoiceManagementInterface
     */
    private $invoiceManagement;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     */
    public function testOrderTotalItemCount()
    {
        $expectedResult = [['total_item_count' => 1]];
        $actualResult = [];
        /** @var Order $order */
        foreach ($this->collection->getItems() as $order) {
            $actualResult[] = ['total_item_count' => $order->getData('total_item_count')];
        }
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Test order with exactly one configurable.
     *
     * @return void
     * @magentoDataFixture Magento/Sales/_files/order_configurable_product.php
     */
    public function testLastInvoiceWithConfigurable(): void
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('increment_id', '100000001')
            ->create();
        $orders = $this->orderRepository->getList($searchCriteria);
        $orders = $orders->getItems();
        $order = array_shift($orders);
        $invoice = $this->invoiceManagement->prepareInvoice($order);

        self::assertEquals($invoice->isLast(), true);
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->collection = $this->objectManager->create(OrderCollection::class);
        $this->invoiceManagement = $this->objectManager->get(InvoiceManagementInterface::class);
        $this->orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);
        $this->searchCriteriaBuilder = $this->objectManager->get(SearchCriteriaBuilder::class);
    }
}
