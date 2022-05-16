<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Controller\Adminhtml;

use Magento\Backend\Helper\Data;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Test class for \Magento\Backend\Controller\Adminhtml\Auth
 * @magentoAppArea Adminhtml
 * @magentoDbIsolation enabled
 */
class AuthTest extends AbstractController
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
     * Check not logged state
     * @covers \Magento\Backend\Controller\Adminhtml\Auth\Login::execute
     */
    public function testNotLoggedLoginAction()
    {
        $this->dispatch('backend/admin/auth/login');
        /** @var $backendUrlModel UrlInterface */
        $backendUrlModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            UrlInterface::class
        );
        $backendUrlModel->turnOffSecretKey();
        $url = $backendUrlModel->getUrl('admin');
        $this->assertRedirect($this->stringStartsWith($url));
    }

    /**
     * Check logged state
     * @covers \Magento\Backend\Controller\Adminhtml\Auth\Login::execute
     * @magentoDbIsolation enabled
     */
    public function testLoggedLoginAction()
    {
        $this->_login();

        $this->dispatch('backend/admin/auth/login');
        /** @var $backendUrlModel UrlInterface */
        $backendUrlModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            UrlInterface::class
        );
        $url = $backendUrlModel->getStartupPageUrl();
        $expected = $backendUrlModel->getUrl($url);
        $this->assertRedirect($this->stringStartsWith($expected));

        $this->_logout();
    }

    /**
     * Performs user login
     */
    protected function _login()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            UrlInterface::class
        )->turnOffSecretKey();

        $this->_auth = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Backend\Model\Auth::class
        );
        $this->_auth->login(
            Bootstrap::ADMIN_NAME,
            Bootstrap::ADMIN_PASSWORD
        );
        $this->_session = $this->_auth->getAuthStorage();
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            UrlInterface::class
        )->turnOnSecretKey();
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testNotLoggedLoginActionWithRedirect()
    {
        /** @var FormKey $formKey */
        $formKey = $this->_objectManager->get(FormKey::class);
        $this->getRequest()->setPostValue(
            [
                'login' => [
                    'username' => Bootstrap::ADMIN_NAME,
                    'password' => Bootstrap::ADMIN_PASSWORD,
                ],
                'form_key' => $formKey->getFormKey(),
            ]
        );

        $this->dispatch('backend/admin/index/index');

        $response = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(ResponseInterface::class);
        $code = $response->getHttpResponseCode();
        $this->assertTrue($code >= 300 && $code < 400, 'Incorrect response code');

        $this->assertTrue(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                \Magento\Backend\Model\Auth::class
            )->isLoggedIn()
        );
    }

    /**
     * @covers \Magento\Backend\Controller\Adminhtml\Auth\Logout::execute
     * @magentoDbIsolation enabled
     */
    public function testLogoutAction()
    {
        $this->_login();
        $this->dispatch('backend/admin/auth/logout');
        $this->assertRedirect(
            $this->equalTo(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                    Data::class
                )->getHomePageUrl()
            )
        );
        $this->assertFalse($this->_session->isLoggedIn(), 'User is not logged out.');
    }

    /**
     * @covers \Magento\Backend\Controller\Adminhtml\Auth\DeniedJson::execute
     * @covers \Magento\Backend\Controller\Adminhtml\Auth\DeniedJson::_getDeniedJson
     * @magentoDbIsolation enabled
     */
    public function testDeniedJsonAction()
    {
        $this->_login();
        $this->dispatch('backend/admin/auth/deniedJson');
        $data = [
            'ajaxExpired' => 1,
            'ajaxRedirect' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                Data::class
            )->getHomePageUrl(),
        ];
        $expected = json_encode($data);
        $this->assertEquals($expected, $this->getResponse()->getBody());
        $this->_logout();
    }

    /**
     * @covers \Magento\Backend\Controller\Adminhtml\Auth\DeniedIframe::execute
     * @covers \Magento\Backend\Controller\Adminhtml\Auth\DeniedIframe::_getDeniedIframe
     * @magentoDbIsolation enabled
     */
    public function testDeniedIframeAction()
    {
        $this->_login();
        $this->dispatch('backend/admin/auth/deniedIframe');
        $homeUrl = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            Data::class
        )->getHomePageUrl();
        $expected = '<script>parent.window.location =';
        $this->assertStringStartsWith($expected, $this->getResponse()->getBody());
        $this->assertStringContainsString($homeUrl, $this->getResponse()->getBody());
        $this->_logout();
    }

    /**
     * Test user logging process when user not assigned to any role
     * @dataProvider incorrectLoginDataProvider
     * @magentoDbIsolation enabled
     *
     * @param $params
     */
    public function testIncorrectLogin($params)
    {
        /** @var FormKey $formKey */
        $formKey = $this->_objectManager->get(FormKey::class);
        $params['form_key'] = $formKey->getFormKey();
        $this->getRequest()->setPostValue($params);
        $this->dispatch('backend/admin/auth/login');
        $this->assertSessionMessages(
            $this->equalTo(
                [
                    'The account sign-in was incorrect or your account is disabled temporarily. '
                    . 'Please wait and try again later.'
                ]
            ),
            MessageInterface::TYPE_ERROR
        );
        $backendUrlModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            UrlInterface::class
        );
        $backendUrlModel->turnOffSecretKey();
        $url = $backendUrlModel->getUrl('admin');
        $this->assertRedirect($this->stringStartsWith($url));
    }

    public function incorrectLoginDataProvider()
    {
        return [
            'login dummy user' => [
                [
                    'login' => [
                        'username' => 'test1',
                        'password' => Bootstrap::ADMIN_PASSWORD,
                    ],
                ],
            ],
            'login without role' => [
                [
                    'login' => [
                        'username' => 'test2',
                        'password' => Bootstrap::ADMIN_PASSWORD,
                    ],
                ],
            ],
            'login not active user' => [
                [
                    'login' => [
                        'username' => 'test3',
                        'password' => Bootstrap::ADMIN_PASSWORD,
                    ],
                ],
            ]
        ];
    }

    protected function tearDown(): void
    {
        $this->_session = null;
        $this->_auth = null;
        parent::tearDown();
    }
}
