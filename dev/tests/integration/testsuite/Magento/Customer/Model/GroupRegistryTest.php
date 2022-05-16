<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Customer\Model\GroupRegistry
 */
class GroupRegistryTest extends TestCase
{
    /**
     * The group code from the fixture data.
     */
    const GROUP_CODE = 'custom_group';

    /**
     * @var GroupRegistry
     */
    protected $_model;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testRetrieve()
    {
        $groupId = $this->_findGroupIdWithCode(self::GROUP_CODE);
        $group = $this->_model->retrieve($groupId);
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals($groupId, $group->getId());
    }

    /**
     * Find the group with a given code.
     *
     * @param string $code
     * @return int
     */
    protected function _findGroupIdWithCode($code)
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var GroupRepositoryInterface $groupRepository */
        $groupRepository = $objectManager->create(GroupRepositoryInterface::class);
        /** @var SearchCriteriaBuilder $searchBuilder */
        $searchBuilder = $objectManager->create(SearchCriteriaBuilder::class);

        foreach ($groupRepository->getList($searchBuilder->create())->getItems() as $group) {
            if ($group->getCode() === $code) {
                return $group->getId();
            }
        }

        return -1;
    }

    /**
     * Ensure can retrieve group with id 0 which is a valid group ID.
     */
    public function testRetrieveGroup0()
    {
        $groupId = 0;
        $group = $this->_model->retrieve($groupId);
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals($groupId, $group->getId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testRetrieveCached()
    {
        $groupId = $this->_findGroupIdWithCode(self::GROUP_CODE);
        $groupBeforeDeletion = $this->_model->retrieve($groupId);
        $group2 = Bootstrap::getObjectManager()
            ->create(Group::class);
        $group2->load($groupId)
            ->delete();
        $groupAfterDeletion = $this->_model->retrieve($groupId);
        $this->assertEquals($groupBeforeDeletion, $groupAfterDeletion);
        $this->assertInstanceOf(Group::class, $groupAfterDeletion);
        $this->assertEquals($groupId, $groupAfterDeletion->getId());
    }

    /**
     */
    public function testRetrieveException()
    {
        $this->expectException(NoSuchEntityException::class);

        $groupId = $this->_findGroupIdWithCode(self::GROUP_CODE);
        $this->_model->retrieve($groupId);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testRemove()
    {
        $this->expectException(NoSuchEntityException::class);

        $groupId = $this->_findGroupIdWithCode(self::GROUP_CODE);
        $group = $this->_model->retrieve($groupId);
        $this->assertInstanceOf(Group::class, $group);
        $group->delete();
        $this->_model->remove($groupId);
        $this->_model->retrieve($groupId);
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()
            ->create(GroupRegistry::class);
    }
}
