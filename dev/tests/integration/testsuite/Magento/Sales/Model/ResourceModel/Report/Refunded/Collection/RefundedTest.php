<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Model\ResourceModel\Report\Refunded\Collection;

use IntlDateFormatter;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Reports\Model\Item;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for refunds reports collection which is used to obtain refund reports by refund date.
 */
class RefundedTest extends TestCase
{
    /**
     * @var Refunded
     */
    private $collection;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @magentoDataFixture Magento/Sales/_files/order_info.php
     * @magentoDataFixture Magento/Sales/_files/order_from_past.php
     * @magentoDataFixture Magento/Sales/_files/report_refunded.php
     * @return void
     */
    public function testGetItems()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->objectManager->create(\Magento\Sales\Model\Order::class);
        $order->loadByIncrementId('100000001');
        $creditmemoCreatedAt = $order->getCreditmemosCollection()->getFirstItem()->getCreatedAt();
        /** @var DateTime $dateTime */
        $dateTime = $this->objectManager->create(DateTimeFactory::class)
            ->create();
        /** @var TimezoneInterface $timezone */
        $timezone = $this->objectManager->create(TimezoneInterface::class);
        $creditmemoCreatedAt = $timezone->formatDateTime(
            $creditmemoCreatedAt,
            IntlDateFormatter::SHORT,
            IntlDateFormatter::NONE,
            null,
            null,
            'yyyy-MM-dd'
        );
        $creditmemoCreatedAtDate = $dateTime->date('Y-m-d', $creditmemoCreatedAt);

        $expectedResult = [
            [
                'orders_count' => 1,
                'refunded' => 50,
                'online_refunded' => 50,
                'offline_refunded' => 0,
                'period' => $creditmemoCreatedAtDate
            ],
        ];
        $actualResult = [];
        /** @var Item $reportItem */
        foreach ($this->collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->collection = $this->objectManager->create(
            Refunded::class
        );
        $this->collection->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter([1]);
    }
}
