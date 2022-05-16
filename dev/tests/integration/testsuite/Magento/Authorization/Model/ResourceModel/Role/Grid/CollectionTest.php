<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Authorization\Model\ResourceModel\Role\Grid;

use Magento\Authorization\Model\Acl\Role\Group;
use Magento\Reports\Model\Item;
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
    private $_collection;

    public function testGetItems()
    {
        $expectedResult = [
            [
                'role_type' => Group::ROLE_TYPE,
                'role_name' => \Magento\TestFramework\Bootstrap::ADMIN_ROLE_NAME,
            ],
        ];
        $actualResult = [];
        /** @var Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }

    protected function setUp(): void
    {
        $this->_collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }
}
