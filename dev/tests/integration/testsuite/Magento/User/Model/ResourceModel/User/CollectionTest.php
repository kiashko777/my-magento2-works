<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\User\Model\ResourceModel\User;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * User collection test
 * @magentoAppArea Adminhtml
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected $_collection;

    public function testFilteringCollectionByUserId()
    {
        $this->assertEquals(1, $this->_collection->addFieldToFilter('user_id', 1)->count());
    }

    protected function setUp(): void
    {
        $this->_collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }
}
