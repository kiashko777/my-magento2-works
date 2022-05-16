<?php
/**
 * \Magento\Customer\Model\ResourceModel\Customer\Collection
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model\ResourceModel\Customer;

use Magento\Framework\DB\Select;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected $_collection;

    public function testAddNameToSelect()
    {
        $this->_collection->addNameToSelect();
        $joinParts = $this->_collection->getSelect()->getPart(Select::FROM);

        $this->assertArrayHasKey('e', $joinParts);
        $this->assertCount(1, $joinParts);
    }

    protected function setUp(): void
    {
        $this->_collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }
}
