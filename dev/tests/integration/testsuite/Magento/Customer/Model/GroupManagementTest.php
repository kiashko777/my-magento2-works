<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\App\MutableScopeConfig;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\Customer\Model\GroupManagement
 */
class GroupManagementTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @param $testGroup
     * @param $storeId
     *
     * @dataProvider getDefaultGroupDataProvider
     */
    public function testGetDefaultGroupWithStoreId($testGroup, $storeId)
    {
        $this->assertDefaultGroupMatches($testGroup, $storeId);
    }

    /**
     * @param $testGroup
     * @param $storeId
     */
    private function assertDefaultGroupMatches($testGroup, $storeId)
    {
        $group = $this->groupManagement->getDefaultGroup($storeId);
        $this->assertEquals($testGroup['id'], $group->getId());
        $this->assertEquals($testGroup['code'], $group->getCode());
        $this->assertEquals($testGroup['tax_class_id'], $group->getTaxClassId());
        $this->assertEquals($testGroup['tax_class_name'], $group->getTaxClassName());
    }

    /**
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     */
    public function testGetDefaultGroupWithNonDefaultStoreId()
    {
        /** @var StoreManagerInterface $storeManager */
        $storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);
        $nonDefaultStore = $storeManager->getStore('secondstore');
        $nonDefaultStoreId = $nonDefaultStore->getId();
        /** @var MutableScopeConfig $scopeConfig */
        $scopeConfig = $this->objectManager->get(MutableScopeConfig::class);
        $scopeConfig->setValue(
            GroupManagement::XML_PATH_DEFAULT_ID,
            2,
            ScopeInterface::SCOPE_STORE,
            'secondstore'
        );
        $testGroup = ['id' => 2, 'code' => 'Wholesale', 'tax_class_id' => 3, 'tax_class_name' => 'Retail Customer'];
        $this->assertDefaultGroupMatches($testGroup, $nonDefaultStoreId);
    }

    /**
     */
    public function testGetDefaultGroupWithInvalidStoreId()
    {
        $this->expectException(NoSuchEntityException::class);

        $storeId = 1234567;
        $this->groupManagement->getDefaultGroup($storeId);
    }

    public function testIsReadonlyWithGroupId()
    {
        $testGroup = ['id' => 3, 'code' => 'General', 'tax_class_id' => 3, 'tax_class_name' => 'Retail Customer'];
        $this->assertFalse($this->groupManagement->isReadonly($testGroup['id']));
    }

    /**
     */
    public function testIsReadonlyWithInvalidGroupId()
    {
        $this->expectException(NoSuchEntityException::class);

        $testGroup = ['id' => 4, 'code' => 'General', 'tax_class_id' => 3, 'tax_class_name' => 'Retail Customer'];
        $this->groupManagement->isReadonly($testGroup['id']);
    }

    public function testGetNotLoggedInGroup()
    {
        $notLoggedInGroup = $this->groupManagement->getNotLoggedInGroup();
        $this->assertEquals(GroupManagement::NOT_LOGGED_IN_ID, $notLoggedInGroup->getId());
    }

    public function testGetLoggedInGroups()
    {
        $loggedInGroups = $this->groupManagement->getLoggedInGroups();
        foreach ($loggedInGroups as $group) {
            $this->assertNotEquals(GroupManagement::NOT_LOGGED_IN_ID, $group->getId());
            $this->assertNotEquals(GroupManagement::CUST_GROUP_ALL, $group->getId());
        }
    }

    public function testGetAllGroup()
    {
        $allGroup = $this->groupManagement->getAllCustomersGroup();
        $this->assertEquals(32000, $allGroup->getId());
    }

    /**
     * @return array
     */
    public function getDefaultGroupDataProvider()
    {
        /** @var StoreManagerInterface $storeManager */
        $storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);
        $defaultStoreId = $storeManager->getStore()->getId();
        return [
            'no store id' => [
                ['id' => 1, 'code' => 'General', 'tax_class_id' => 3, 'tax_class_name' => 'Retail Customer'],
                null,
            ],
            'default store id' => [
                ['id' => 1, 'code' => 'General', 'tax_class_id' => 3, 'tax_class_name' => 'Retail Customer'],
                $defaultStoreId,
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->groupManagement = $this->objectManager->get(GroupManagementInterface::class);
    }
}
