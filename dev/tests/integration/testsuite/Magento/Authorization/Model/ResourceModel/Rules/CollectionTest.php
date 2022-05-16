<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Authorization\Model\ResourceModel\Rules;

use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected $_collection;

    public function testGetByRoles()
    {
        $user = Bootstrap::getObjectManager()->create(User::class);
        $user->loadByUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);
        $this->_collection->getByRoles($user->getRole()->getId());

        $where = $this->_collection->getSelect()->getPart(Select::WHERE);
        /** @var Mysql $connection */
        $connection = $this->_collection->getConnection();
        $quote = $connection->getQuoteIdentifierSymbol();
        $this->assertContains("({$quote}role_id{$quote} = '" . $user->getRole()->getId() . "')", $where);
    }

    public function testAddSortByLength()
    {
        $this->_collection->addSortByLength();

        $order = $this->_collection->getSelect()->getPart(Select::ORDER);
        $this->assertContains(['length', 'DESC'], $order);
    }

    protected function setUp(): void
    {
        $this->_collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }
}
