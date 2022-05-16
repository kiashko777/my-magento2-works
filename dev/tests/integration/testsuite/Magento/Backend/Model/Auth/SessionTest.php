<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Model\Auth;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Backend\Model\Auth;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Bootstrap as TestHelper;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class SessionTest extends TestCase
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @dataProvider loginDataProvider
     */
    public function testIsLoggedIn($loggedIn)
    {
        if ($loggedIn) {
            $this->auth->login(
                TestHelper::ADMIN_NAME,
                TestHelper::ADMIN_PASSWORD
            );
        }
        $this->assertEquals($loggedIn, $this->authSession->isLoggedIn());
    }

    public function loginDataProvider()
    {
        return [[false], [true]];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->objectManager->get(ScopeInterface::class)
            ->setCurrentScope(FrontNameResolver::AREA_CODE);
        $this->auth = $this->objectManager->create(Auth::class);
        $this->authSession = $this->objectManager->create(Session::class);
        $this->auth->setAuthStorage($this->authSession);
        $this->auth->logout();
    }

    protected function tearDown(): void
    {
        $this->auth = null;
        $this->objectManager->get(ScopeInterface::class)->setCurrentScope(null);
    }
}
