<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Security\Model;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Backend\Model\Auth;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Security\Model\ResourceModel\AdminSessionInfo\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class AdminSessionsManagerTest extends TestCase
{
    /**
     * @var Auth
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
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Test if current admin user is logged out
     *
     * @magentoDbIsolation enabled
     */
    public function testProcessLogout()
    {
        $this->auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
        $sessionId = $this->authSession->getSessionId();
        $this->auth->logout();
        $this->adminSessionInfo->load($sessionId, 'session_id');
        $this->assertEquals($this->adminSessionInfo->getStatus(), AdminSessionInfo::LOGGED_OUT);
    }

    /**
     * Test if the admin session is created in database
     *
     * @magentoDbIsolation enabled
     */
    public function testIsAdminSessionIsCreated()
    {
        $this->auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
        $sessionId = $this->authSession->getSessionId();
        $this->adminSessionInfo->load($sessionId, 'session_id');
        $this->assertGreaterThanOrEqual(1, (int)$this->adminSessionInfo->getId());
        $this->auth->logout();
    }

    /**
     * Test if other sessions are terminated if admin_account_sharing is disabled
     *
     * @magentoAdminConfigFixture admin/security/session_lifetime 100
     * @magentoConfigFixture default_store admin/security/admin_account_sharing 0
     * @magentoDbIsolation enabled
     */
    public function testTerminateOtherSessionsProcessLogin()
    {
        $session = $this->objectManager->create(AdminSessionInfo::class);
        $session->setSessionId('669e2e3d752e8')
            ->setUserId(1)
            ->setStatus(1)
            ->setCreatedAt(time() - 10)
            ->setUpdatedAt(time() - 9)
            ->save();
        $this->auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
        $session->load('669e2e3d752e8', 'session_id');
        $this->assertEquals(
            AdminSessionInfo::LOGGED_OUT_BY_LOGIN,
            (int)$session->getStatus()
        );
    }

    /**
     * Test if current session is retrieved
     *
     * @magentoDbIsolation enabled
     */
    public function testGetCurrentSession()
    {
        $this->auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
        $sessionId = $this->authSession->getSessionId();
        $this->adminSessionInfo->load($sessionId, 'session_id');
        $this->assertEquals(
            $this->adminSessionInfo->getSessionId(),
            $this->adminSessionsManager->getCurrentSession()->getSessionId()
        );
    }

    /**
     * Test if other sessions were logged out if logoutOtherUserSessions() action was performed
     *
     * @magentoAdminConfigFixture admin/security/session_lifetime 100
     * @magentoConfigFixture default_store admin/security/admin_account_sharing 1
     * @magentoDbIsolation enabled
     */
    public function testLogoutOtherUserSessions()
    {
        /** @var AdminSessionInfo $session */
        $session = $this->objectManager->create(AdminSessionInfo::class);
        $session->setSessionId('669e2e3d752e8')
            ->setUserId(1)
            ->setStatus(1)
            ->setCreatedAt(time() - 50)
            ->setUpdatedAt(time() - 49)
            ->save();
        $this->auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
        $collection = $this->getCollectionForLogoutOtherUserSessions($session);
        $this->assertGreaterThanOrEqual(1, $collection->getSize());
        $this->adminSessionsManager->logoutOtherUserSessions();
        $collection = $this->getCollectionForLogoutOtherUserSessions($session);
        $this->assertEquals(0, $collection->getSize());
    }

    /**
     * Collection getter with filters populated for testLogoutOtherUserSessions() method
     *
     * @param AdminSessionInfo $session
     * @return ResourceModel\AdminSessionInfo\Collection
     */
    protected function getCollectionForLogoutOtherUserSessions(AdminSessionInfo $session)
    {
        /** @var Collection $collection */
        $collection = $session->getResourceCollection();
        $collection->filterByUser(
            $this->authSession->getUser()->getId(),
            AdminSessionInfo::LOGGED_IN,
            $this->authSession->getSessionId()
        )
            ->filterExpiredSessions(100)
            ->load();

        return $collection;
    }

    /**
     * Test for cleanExpiredSessions() method
     *
     * @magentoDataFixture Magento/Security/_files/adminsession.php
     * @magentoAdminConfigFixture admin/security/session_lifetime 1
     * @magentoDbIsolation enabled
     */
    public function testCleanExpiredSessions()
    {
        /** @var AdminSessionInfo $session */
        $session = $this->objectManager->create(AdminSessionInfo::class);
        $collection = $this->getCollectionForCleanExpiredSessions($session);
        $sizeBefore = $collection->getSize();
        $this->adminSessionsManager->cleanExpiredSessions();
        $collection = $this->getCollectionForCleanExpiredSessions($session);
        $sizeAfter = $collection->getSize();
        $this->assertGreaterThan($sizeAfter, $sizeBefore);
    }

    /**
     * Collection getter with filters populated for testCleanExpiredSessions() method
     *
     * @param AdminSessionInfo $session
     * @return ResourceModel\AdminSessionInfo\Collection
     */
    protected function getCollectionForCleanExpiredSessions(AdminSessionInfo $session)
    {
        /** @var Collection $collection */
        $collection = $session->getResourceCollection()
            ->load();

        return $collection;
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
        $this->auth = $this->objectManager->create(Auth::class);
        $this->authSession = $this->objectManager->create(Session::class);
        $this->adminSessionInfo = $this->objectManager->create(AdminSessionInfo::class);
        $this->auth->setAuthStorage($this->authSession);
        $this->messageManager = $this->objectManager->get(ManagerInterface::class);
        $this->adminSessionsManager = $this->objectManager->create(AdminSessionsManager::class);
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
