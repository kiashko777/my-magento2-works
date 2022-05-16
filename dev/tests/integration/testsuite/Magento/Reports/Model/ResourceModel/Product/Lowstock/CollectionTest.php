<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Model\ResourceModel\Product\Lowstock;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Reports\Model\ResourceModel\Products\Lowstock\Collection
 */
class CollectionTest extends TestCase
{

    /**
     * @var Collection
     */
    private $collection;

    /**
     * Assert that filterByProductType method throws LocalizedException if not String or Array is passed to it
     *
     */
    public function testFilterByProductTypeException()
    {
        $this->expectException(LocalizedException::class);

        $this->collection->filterByProductType(100);
    }

    /**
     * Assert that String argument passed to filterByProductType method is correctly passed to attribute adder
     *
     */
    public function testFilterByProductTypeString()
    {
        $this->collection->filterByProductType('simple');
        $whereParts = $this->collection->getSelect()->getPart(Select::WHERE);
        $this->assertStringContainsString('simple', $whereParts[0]);
    }

    /**
     * Assert that Array argument passed to filterByProductType method is correctly passed to attribute adder
     *
     */
    public function testFilterByProductTypeArray()
    {
        $this->collection->filterByProductType(['simple', 'configurable']);
        $whereParts = $this->collection->getSelect()->getPart(Select::WHERE);

        $this->assertThat(
            $whereParts[0],
            $this->logicalAnd(
                $this->stringContains('simple'),
                $this->stringContains('configurable')
            )
        );
    }

    protected function setUp(): void
    {
        /**
         * @var  Collection
         */
        $this->collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }
}
