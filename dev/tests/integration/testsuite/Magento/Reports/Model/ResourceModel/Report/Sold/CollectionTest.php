<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Model\ResourceModel\Report\Sold;

use Magento\Reports\Model\ResourceModel\Product\Sold\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class CollectionTest
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @magentoDataFixture Magento/Sales/_files/order_item_with_configurable_for_reorder.php
     */
    public function testFilterByProductTypeException()
    {
        $items = $this->collection->addOrderedQty()->getItems();
        $this->assertCount(1, $items);
        $orderItem = array_shift($items);
        $this->assertEquals('1.0000', $orderItem['ordered_qty']);
        $this->assertEquals('Configurable Products', $orderItem['order_items_name']);
        //verify if order_item_sku exists in return data
        $this->assertEquals('simple_20', $orderItem['order_items_sku']);
    }

    protected function setUp(): void
    {
        /**
         * @var Collection
         */
        $this->collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }
}
