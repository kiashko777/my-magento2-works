<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Model;

use Magento\Directory\Model\Currency;
use Magento\Framework\DB\Select;
use Magento\TestFramework\Entity;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class WebsiteTest extends TestCase
{
    /**
     * @var Website
     */
    protected $_model;

    /**
     * @magentoDbIsolation enabled
     */
    public function testLoadById()
    {
        $this->assertEquals(1, $this->_model->getId());
        $this->assertEquals('base', $this->_model->getCode());
        $this->assertEquals('Main Website', $this->_model->getName());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testLoadByCode()
    {
        $this->_model->load('admin');
        $this->assertEquals(0, $this->_model->getId());
        $this->assertEquals('admin', $this->_model->getCode());
        $this->assertEquals('Admin', $this->_model->getName());
    }

    /**
     * @covers \Magento\Store\Model\Website::setGroups
     * @covers \Magento\Store\Model\Website::setStores
     * @covers \Magento\Store\Model\Website::getStores
     */
    public function testSetGroupsAndStores()
    {
        /* Groups */
        $expectedGroup = Bootstrap::getObjectManager()->create(
            Group::class
        );
        $expectedGroup->setId(123);
        $this->_model->setDefaultGroupId($expectedGroup->getId());
        $this->_model->setGroups([$expectedGroup]);

        $groups = $this->_model->getGroups();
        $this->assertSame($expectedGroup, reset($groups));

        /* Stores */
        $expectedStore = Bootstrap::getObjectManager()->create(
            Store::class
        );
        $expectedStore->setId(456);
        $expectedGroup->setDefaultStoreId($expectedStore->getId());
        $this->_model->setStores([$expectedStore]);

        $stores = $this->_model->getStores();
        $this->assertSame($expectedStore, reset($stores));
    }

    public function testGetGroups()
    {
        $groups = $this->_model->getGroups();
        $this->assertEquals([1], array_keys($groups));
        $this->assertInstanceOf(Group::class, $groups[1]);
        $this->assertEquals(1, $groups[1]->getId());
    }

    public function testGetGroupIds()
    {
        $this->assertEquals([1 => 1], $this->_model->getGroupIds());
    }

    public function testGetGroupsCount()
    {
        $this->assertEquals(1, $this->_model->getGroupsCount());
    }

    public function testGetDefaultGroup()
    {
        $defaultGroup = $this->_model->getDefaultGroup();
        $this->assertInstanceOf(Group::class, $defaultGroup);
        $this->assertEquals(1, $defaultGroup->getId());

        $this->_model->setDefaultGroupId(null);
        $this->assertFalse($this->_model->getDefaultGroup());
    }

    public function testGetStores()
    {
        $stores = $this->_model->getStores();
        $this->assertEquals([1], array_keys($stores));
        $this->assertInstanceOf(Store::class, $stores[1]);
        $this->assertEquals(1, $stores[1]->getId());
    }

    public function testGetStoreIds()
    {
        $this->assertEquals([1 => 1], $this->_model->getStoreIds());
    }

    public function testGetStoreCodes()
    {
        $this->assertEquals([1 => 'default'], $this->_model->getStoreCodes());
    }

    public function testGetStoresCount()
    {
        $this->assertEquals(1, $this->_model->getStoresCount());
    }

    public function testIsCanDelete()
    {
        $this->assertFalse($this->_model->isCanDelete());
        $this->_model->isReadOnly(true);
        $this->assertFalse($this->_model->isCanDelete());
    }

    public function testGetWebsiteGroupStore()
    {
        $this->assertEquals('1--', $this->_model->getWebsiteGroupStore());
        $this->_model->setGroupId(123);
        $this->_model->setStoreId(456);
        $this->assertEquals('1-123-456', $this->_model->getWebsiteGroupStore());
    }

    public function testGetDefaultGroupId()
    {
        $this->assertEquals(1, $this->_model->getDefaultGroupId());
    }

    public function testGetBaseCurrency()
    {
        $currency = $this->_model->getBaseCurrency();
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('USD', $currency->getCode());
    }

    public function testGetDefaultStore()
    {
        $defaultStore = $this->_model->getDefaultStore();
        $this->assertInstanceOf(Store::class, $defaultStore);
        $this->assertEquals(1, $defaultStore->getId());
    }

    public function testGetDefaultStoresSelect()
    {
        $this->assertInstanceOf(Select::class, $this->_model->getDefaultStoresSelect());
    }

    public function testIsReadonly()
    {
        $this->assertFalse($this->_model->isReadOnly());
        $this->_model->isReadOnly(true);
        $this->assertTrue($this->_model->isReadOnly());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea Adminhtml
     */
    public function testCRUD()
    {
        $this->_model->setData(['code' => 'test_website', 'name' => 'test website', 'default_group_id' => 1]);

        /* emulate admin store */
        $crud = new Entity($this->_model, ['name' => 'new name']);
        $crud->testCrud();
    }

    public function testCollection()
    {
        $collection = $this->_model->getCollection()->joinGroupAndStore()->addIdFilter(1);
        $this->assertCount(1, $collection->getItems());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Website::class
        );
        $this->_model->load(1);
    }
}
