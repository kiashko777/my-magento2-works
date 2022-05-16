<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Model\ResourceModel\Review\Customer;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * This tests covers issue described in:
     * https://github.com/magento/magento2/issues/10301
     *
     * @magentoDataFixture Magento/Review/_files/customer_review.php
     */
    public function testSelectCountSql()
    {
        $this->collection->addFieldToFilter('customer_name', ['like' => '%John%'])->getItems();
        $this->assertEquals(1, $this->collection->getSize());
    }

    protected function setUp(): void
    {
        $this->collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }
}
