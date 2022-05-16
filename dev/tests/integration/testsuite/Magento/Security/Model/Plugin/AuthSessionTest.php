<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Security\Model\Plugin;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Security\Api\Data\UserExpirationInterface;
use Magento\Security\Api\Data\UserExpirationInterfaceFactory;
use Magento\Security\Model\AdminSessionInfo;
use Magento\Security\Model\AdminSessionsManager;
use Magento\Security\Model\ConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @magentoAppIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthSessionTest extends TestCase
{
    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $auth;

    /**
     * @var Session
     */
    protected $authSession;

    /**
     * @var AdminSessionInfo
     */
    protected $adminSessionInfo;

    /**
     * @var AdminSessionsManager
     */
    protected $adminSessionsManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var ConfigInterface
     */
    protected $securityConfig;

    /**
     * Test of prolong user action
     * session manager will not trigger new prolong if previous prolong was less than X sec ago
     * X - is calculated based on current admin session lifetime
     *
     * @see \Magento\Security\Model\AdminSessionsManager::lastProlongIsOldEnough
     * @magentoDbIsolation enabled
     */
    public function testConsecutiveProcessProlong()
    {
        $this->auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
        $sessionId = $this->authSession->getSessionId();
        $prolongsDiff = log($this->securityConfig->getAdminSessionLifetime()) - 2; // X from comment above
        $dateInPast = $this->dateTime->formatDate($this->authSession->getUpdatedAt() - $prolongsDiff);
        $this->adminSessionsManager->getCurrentSession()
            ->setData(
                'updated_at',
                $dateInPast
            )
            ->save();
        $this->adminSessionInfo->load($sessionId, 'session_id');
        $oldUpdatedAt = $this->adminSessionInfo->getUpdatedAt();
        $this->authSession->prolong();
        $this->adminSessionInfo->load($sessionId, 'session_id');
        $updatedAt = $this->adminSessionInfo->getUpdatedAt();

        $this->assertSame(strtotime($oldUpdatedAt), strtotime($updatedAt));
    }

    /**
     * Test of prolong user action
     * session manager will trigger new prolong if previous prolong was more than X sec ago
     * X - is calculated based on current admin session lifetime
     *
     * @see \Magento\Security\Model\AdminSessionsManager::lastProlongIsOldEnough
     * @magentoDbIsolation enabled
     */
    public function testProcessProlong()
    {
        $this->auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
        $sessionId = $this->authSession->getSessionId();
        $prolongsDiff = 4 * log($this->securityConfig->getAdminSessionLifetime()) + 2; // X from comment above
        $dateInPast = $this->dateTime->formatDate($this->authSession->getUpdatedAt() - $prolongsDiff);
        $this->adminSessionsManager->getCurrentSession()
            ->setData(
                'updated_at',
                $dateInPast
            )
            ->save();
        $this->adminSessionInfo->load($sessionId, 'session_id');
        $oldUpdatedAt = $this->adminSessionInfo->getUpdatedAt();
        $this->authSession->prolong();
        $this->adminSessionInfo->load($sessionId, 'session_id');
        $updatedAt = $this->adminSessionInfo->getUpdatedAt();

        $this->assertGreaterThan(strtotime($oldUpdatedAt), strtotime($updatedAt));
    }

    /**
     * Test processing prolong with an expired user.
     *
     * @magentoDbIsolation enabled
     */
    public function testProcessProlongWithExpiredUser()
    {
        $this->auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );

        $expireDate = new \DateTime();
        $expireDate->modify('-10 days');
        /** @var User $user */
        $user = $this->objectManager->create(User::class);
        $user->loadByUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);
        $userExpirationFactory =
            $this->objectManager->create(UserExpirationInterfaceFactory::class);
        /** @var UserExpirationInterface $userExpiration */
        $userExpiration = $userExpirationFactory->create();
        $userExpiration->setId($user->getId())
            ->setExpiresAt($expireDate->format('Y-m-d H:i:s'))
            ->save();

        // need to trigger a prolong
        $sessionId = $this->authSession->getSessionId();
        $prolongsDiff = 4 * log($this->securityConfig->getAdminSessionLifetime()) + 2;
        $dateInPast = $this->dateTime->formatDate($this->authSession->getUpdatedAt() - $prolongsDiff);
        $this->adminSessionsManager->getCurrentSession()
            ->setData(
                'updated_at',
                $dateInPast
            )
            ->save();
        $this->adminSessionInfo->load($sessionId, 'session_id');
        $this->authSession->prolong();
        static::assertFalse($this->auth->isLoggedIn());
        $user->reload();
        static::assertFalse((bool)$user->getIsActive());
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->objectManager->get(ScopeInterface::class)
            ->setCurrentScope(FrontNameResolver::AREA_CODE);
        $this->auth = $this->objectManager->create(\Magento\Backend\Model\Auth::class);
        $this->authSession = $this->objectManager->create(Session::class);
        $this->adminSessionInfo = $this->objectManager->create(AdminSessionInfo::class);
        $this->auth->setAuthStorage($this->authSession);
        $this->adminSessionsManager = $this->objectManager->get(AdminSessionsManager::class);
        $this->dateTime = $this->objectManager->create(DateTime::class);
        $this->securityConfig = $this->objectManager->create(ConfigInterface::class);
    }

    /**
     * Tear down
     */
    protected function tearDown(): void
    {
        $this->auth = null;
        $this->authSession = null;
        $this->adminSessionInfo = null;
        $this->adminSessionsManager = null;
        $this->objectManager = null;
        parent::tearDown();
    }
}
