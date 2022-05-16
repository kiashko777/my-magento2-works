<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Security\Model\ResourceModel\UserExpiration;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * Test UserExpiration collection filters.
 */
class CollectionTest extends TestCase
{
    /**
     * @var CollectionFactory
     */
    protected $collectionModelFactory;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @magentoDataFixture Magento/Security/_files/expired_users.php
     */
    public function testAddExpiredActiveUsersFilter()
    {
        /** @var Collection $collectionModel */
        $collectionModel = $this->collectionModelFactory->create();
        $collectionModel->addActiveExpiredUsersFilter();
        static::assertEquals(1, $collectionModel->getSize());
    }

    /**
     * @magentoDataFixture Magento/Security/_files/expired_users.php
     */
    public function testAddUserIdsFilter()
    {
        $adminUserNameFromFixture = 'adminUserExpired';
        $user = $this->objectManager->create(User::class);
        $user->loadByUsername($adminUserNameFromFixture);

        /** @var Collection $collectionModel */
        $collectionModel = $this->collectionModelFactory->create()->addUserIdsFilter([$user->getId()]);
        static::assertEquals(1, $collectionModel->getSize());
    }

    /**
     * @magentoDataFixture Magento/Security/_files/expired_users.php
     */
    public function testAddExpiredRecordsForUserFilter()
    {
        $adminUserNameFromFixture = 'adminUserExpired';
        $user = $this->objectManager->create(User::class);
        $user->loadByUsername($adminUserNameFromFixture);

        /** @var Collection $collectionModel */
        $collectionModel = $this->collectionModelFactory->create()->addExpiredRecordsForUserFilter($user->getId());
        static::assertEquals(1, $collectionModel->getSize());
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->collectionModelFactory = $this->objectManager
            ->create(CollectionFactory::class);
    }
}
