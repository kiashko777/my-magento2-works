<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Review\Model\ResourceModel\Rating;

use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @magentoDataFixture Magento/Review/_files/customer_review_with_rating.php
     */
    public function testAddEntitySummaryToItem()
    {
        $ratingData = Bootstrap::getObjectManager()
            ->get(Registry::class)
            ->registry('rating_data');

        $result = $this->collection->addEntitySummaryToItem($ratingData->getEntityId(), $ratingData->getStoreId());
        $this->assertEquals($this->collection, $result);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testAddEntitySummaryToItemEmpty()
    {
        foreach ($this->collection->getItems() as $item) {
            $item->delete();
        }
        $this->collection->clear();
        $result = $this->collection->addEntitySummaryToItem(1, 1);
        $this->assertEquals($this->collection, $result);
    }

    public function testAddStoreData()
    {
        $this->collection->addStoreData();
    }

    public function testSetStoreFilter()
    {
        $this->collection->setStoreFilter(1);
    }

    protected function setUp(): void
    {
        $this->collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }
}
