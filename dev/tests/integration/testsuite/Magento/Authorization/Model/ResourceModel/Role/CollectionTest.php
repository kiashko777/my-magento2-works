<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Authorization\Model\ResourceModel\Role;

use Magento\Authorization\Model\UserContextInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * Role collection test
 * @magentoAppArea Adminhtml
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected $_collection;

    public function testSetUserFilter()
    {
        $user = Bootstrap::getObjectManager()->create(User::class);
        $user->loadByUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);
        $this->_collection->setUserFilter($user->getId(), UserContextInterface::USER_TYPE_ADMIN);

        $selectQueryStr = $this->_collection->getSelect()->__toString();

        $this->assertStringContainsString('user_id', $selectQueryStr);
        $this->assertStringContainsString('user_type', $selectQueryStr);
    }

    public function testSetRolesFilter()
    {
        $this->_collection->setRolesFilter();

        $this->assertStringContainsString('role_type', $this->_collection->getSelect()->__toString());
    }

    public function testToOptionArray()
    {
        $this->assertNotEmpty($this->_collection->toOptionArray());

        foreach ($this->_collection->toOptionArray() as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
        }
    }

    protected function setUp(): void
    {
        $this->_collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }
}
