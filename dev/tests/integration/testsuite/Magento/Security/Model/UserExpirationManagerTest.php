<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Security\Model;

use DateTime;
use Exception;
use Magento\Backend\Model\Auth;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Security\Api\Data\UserExpirationInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * Tests for \Magento\Security\Model\UserExpirationManager
 * @magentoAppArea Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserExpirationManagerTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var AdminSessionInfo
     */
    private $adminSessionInfo;

    /**
     * @var UserExpirationManager
     */
    private $userExpirationManager;

    /**
     * @magentoDataFixture Magento/Security/_files/expired_users.php
     */
    public function testUserIsExpired()
    {
        $adminUserNameFromFixture = 'adminUserExpired';
        $user = $this->loadUserByUsername($adminUserNameFromFixture);
        static::assertTrue($this->userExpirationManager->isUserExpired($user->getId()));
    }

    /**
     * @param $username
     * @return User
     */
    private function loadUserByUsername(string $username): User
    {
        /** @var User $user */
        $user = $this->objectManager->create(User::class);
        $user->loadByUsername($username);
        return $user;
    }

    /**
     * @magentoDataFixture Magento/Security/_files/expired_users.php
     * @magentoAppIsolation enabled
     */
    public function testDeactivateExpiredUsersWithExpiredUser()
    {
        $adminUsernameFromFixture = 'adminUserNotExpired';
        $this->loginUser($adminUsernameFromFixture);
        $user = $this->loadUserByUsername($adminUsernameFromFixture);
        $sessionId = $this->authSession->getSessionId();
        $this->expireUser($user);
        $this->userExpirationManager->deactivateExpiredUsersById([$user->getId()]);
        $this->adminSessionInfo->load($sessionId, 'session_id');
        $user->reload();
        $userExpirationModel = $this->loadExpiredUserModelByUser($user);
        static::assertEquals(0, $user->getIsActive());
        static::assertNull($userExpirationModel->getId());
        static::assertEquals(AdminSessionInfo::LOGGED_OUT, (int)$this->adminSessionInfo->getStatus());
    }

    /**
     * Login the given user and return a user model.
     *
     * @param string $username
     * @throws AuthenticationException
     */
    private function loginUser(string $username)
    {
        $this->auth->login(
            $username,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
    }

    /**
     * Expire the given user and return the UserExpiration model.
     *
     * @param User $user
     * @throws Exception
     */
    private function expireUser(User $user)
    {
        $expireDate = new DateTime();
        $expireDate->modify('-10 days');
        /** @var UserExpirationInterface $userExpiration */
        $userExpiration = $this->objectManager->create(UserExpirationInterface::class);
        $userExpiration->setId($user->getId())
            ->setExpiresAt($expireDate->format('Y-m-d H:i:s'))
            ->save();
    }

    /**
     * @param User $user
     * @return UserExpiration
     */
    private function loadExpiredUserModelByUser(User $user): UserExpiration
    {
        /** @var UserExpiration $expiredUserModel */
        $expiredUserModel = $this->objectManager->create(UserExpiration::class);
        $expiredUserModel->load($user->getId());
        return $expiredUserModel;
    }

    /**
     * @magentoDataFixture Magento/Security/_files/expired_users.php
     * @magentoAppIsolation enabled
     */
    public function testDeactivateExpiredUsersWithNonExpiredUser()
    {
        $adminUsernameFromFixture = 'adminUserNotExpired';
        $this->loginUser($adminUsernameFromFixture);
        $user = $this->loadUserByUsername($adminUsernameFromFixture);
        $sessionId = $this->authSession->getSessionId();
        $this->userExpirationManager->deactivateExpiredUsersById([$user->getId()]);
        $user->reload();
        $userExpirationModel = $this->loadExpiredUserModelByUser($user);
        $this->adminSessionInfo->load($sessionId, 'session_id');
        static::assertEquals(1, $user->getIsActive());
        static::assertEquals($user->getId(), $userExpirationModel->getId());
        static::assertEquals(AdminSessionInfo::LOGGED_IN, (int)$this->adminSessionInfo->getStatus());
    }

    /**
     * Test deactivating without inputting a user.
     *
     * @magentoDataFixture Magento/Security/_files/expired_users.php
     */
    public function testDeactivateExpiredUsers()
    {
        $notExpiredUser = $this->loadUserByUsername('adminUserNotExpired');
        $expiredUser = $this->loadUserByUsername('adminUserExpired');
        $this->userExpirationManager->deactivateExpiredUsers();
        $notExpiredUserExpirationModel = $this->loadExpiredUserModelByUser($notExpiredUser);
        $expiredUserExpirationModel = $this->loadExpiredUserModelByUser($expiredUser);

        static::assertNotNull($notExpiredUserExpirationModel->getId());
        static::assertNull($expiredUserExpirationModel->getId());
        $notExpiredUser->reload();
        $expiredUser->reload();
        static::assertEquals($notExpiredUser->getIsActive(), 1);
        static::assertEquals($expiredUser->getIsActive(), 0);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->auth = $this->objectManager->create(Auth::class);
        $this->authSession = $this->objectManager->create(Session::class);
        $this->adminSessionInfo = $this->objectManager->create(AdminSessionInfo::class);
        $this->auth->setAuthStorage($this->authSession);
        $this->userExpirationManager =
            $this->objectManager->create(UserExpirationManager::class);
    }
}
