<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\TestCase;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Acl\Builder;
use Magento\Framework\Authorization;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Security\Model\Plugin\Auth;
use Magento\TestFramework\Bootstrap;
use Magento\TestFramework\ObjectManager;

/**
 * A parent class for backend controllers - contains directives for admin user creation and authentication.
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractBackendController extends AbstractController
{
    /**
     * @var Session
     */
    protected $_session;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * The resource used to authorize action
     *
     * @var string
     */
    protected $resource = null;

    /**
     * The uri at which to access the controller
     *
     * @var string
     */
    protected $uri = null;

    /**
     * @var string|null
     */
    protected $httpMethod;

    /**
     * Expected no access response
     *
     * @var int
     */
    protected $expectedNoAccessResponseCode = 403;

    /**
     * Test ACL configuration for action working.
     */
    public function testAclHasAccess()
    {
        if ($this->uri === null) {
            $this->markTestIncomplete('AclHasAccess test is not complete');
        }
        if ($this->httpMethod) {
            $this->getRequest()->setMethod($this->httpMethod);
        }
        $this->dispatch($this->uri);
        $this->assertNotSame(404, $this->getResponse()->getHttpResponseCode());
        $this->assertNotSame($this->expectedNoAccessResponseCode, $this->getResponse()->getHttpResponseCode());
    }

    /**
     * Test ACL actually denying access.
     */
    public function testAclNoAccess()
    {
        if ($this->resource === null || $this->uri === null) {
            $this->markTestIncomplete('Acl test is not complete');
        }
        if ($this->httpMethod) {
            $this->getRequest()->setMethod($this->httpMethod);
        }
        $this->_objectManager->get(Builder::class)
            ->getAcl()
            ->deny(null, $this->resource);
        $this->dispatch($this->uri);
        $this->assertSame($this->expectedNoAccessResponseCode, $this->getResponse()->getHttpResponseCode());
    }

    /**
     * @inheritDoc
     *
     * @throws AuthenticationException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->_objectManager->get(UrlInterface::class)->turnOffSecretKey();
        /**
         * Authorization can be created on test bootstrap...
         * If it will be created on test bootstrap we will have invalid RoleLocator object.
         * As tests by default are run not from Adminhtml area...
         */
        ObjectManager::getInstance()->removeSharedInstance(
            Authorization::class
        );
        $this->_auth = $this->_objectManager->get(\Magento\Backend\Model\Auth::class);
        $this->_session = $this->_auth->getAuthStorage();
        $credentials = $this->_getAdminCredentials();
        $this->_auth->login($credentials['user'], $credentials['password']);
        $this->_objectManager->get(Auth::class)->afterLogin($this->_auth);
    }

    /**
     * Get credentials to login admin user
     *
     * @return array
     */
    protected function _getAdminCredentials()
    {
        return [
            'user' => Bootstrap::ADMIN_NAME,
            'password' => Bootstrap::ADMIN_PASSWORD
        ];
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $this->_auth->getAuthStorage()->destroy(['send_expire_cookie' => false]);
        $this->_auth = null;
        $this->_session = null;
        $this->_objectManager->get(UrlInterface::class)->turnOnSecretKey();
        parent::tearDown();
    }
}
