<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Helper;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Backend\Model\Auth;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Config\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var Auth
     */
    protected $_auth;

    /**
     * @covers \Magento\Backend\Helper\Data::getPageHelpUrl
     * @covers \Magento\Backend\Helper\Data::setPageHelpUrl
     * @covers \Magento\Backend\Helper\Data::addPageHelpUrl
     */
    public function testPageHelpUrl()
    {
        Bootstrap::getObjectManager()->get(
            RequestInterface::class
        )->setControllerModule(
            'dummy'
        )->setControllerName(
            'index'
        )->setActionName(
            'test'
        );

        $expected = 'http://www.magentocommerce.com/gethelp/en_US/dummy/index/test/';
        $this->assertEquals($expected, $this->_helper->getPageHelpUrl(), 'Incorrect help Url');

        $this->_helper->addPageHelpUrl('dummy');
        $expected .= 'dummy';
        $this->assertEquals($expected, $this->_helper->getPageHelpUrl(), 'Incorrect help Url suffix');
    }

    /**
     * @covers \Magento\Backend\Helper\Data::getCurrentUserId
     */
    public function testGetCurrentUserId()
    {
        $this->assertFalse($this->_helper->getCurrentUserId());

        /**
         * perform login
         */
        Bootstrap::getObjectManager()->get(
            UrlInterface::class
        )->turnOffSecretKey();

        $auth = Bootstrap::getObjectManager()->create(Auth::class);
        $auth->login(\Magento\TestFramework\Bootstrap::ADMIN_NAME, \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD);
        $this->assertEquals(1, $this->_helper->getCurrentUserId());

        /**
         * perform logout
         */
        $auth->logout();
        Bootstrap::getObjectManager()->get(
            UrlInterface::class
        )->turnOnSecretKey();

        $this->assertFalse($this->_helper->getCurrentUserId());
    }

    /**
     * @covers \Magento\Backend\Helper\Data::prepareFilterString
     */
    public function testPrepareFilterString()
    {
        $expected = ['key1' => 'val1', 'key2' => 'val2', 'key3' => 'val3'];

        $filterString = base64_encode('key1=' . rawurlencode('val1') . '&key2=' . rawurlencode('val2') . '&key3=val3');
        $actual = $this->_helper->prepareFilterString($filterString);
        $this->assertEquals($expected, $actual);
    }

    public function testGetHomePageUrl()
    {
        $this->assertStringEndsWith(
            'index.php/backend/admin/',
            $this->_helper->getHomePageUrl(),
            'Incorrect home page URL'
        );
    }

    protected function setUp(): void
    {
        Bootstrap::getObjectManager()->get(
            ScopeInterface::class
        )->setCurrentScope(
            FrontNameResolver::AREA_CODE
        );
        $this->_helper = Bootstrap::getObjectManager()->get(
            Data::class
        );
    }

    protected function tearDown(): void
    {
        $this->_helper = null;
        $this->_auth = null;
        Bootstrap::getObjectManager()->get(
            ScopeInterface::class
        )->setCurrentScope(
            null
        );
    }

    /**
     * Performs user login
     */
    protected function _login()
    {
        Bootstrap::getObjectManager()->get(
            UrlInterface::class
        )->turnOffSecretKey();
        $this->_auth = Bootstrap::getObjectManager()->get(
            Auth::class
        );
        $this->_auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        Bootstrap::getObjectManager()->get(
            UrlInterface::class
        )->turnOnSecretKey();
    }
}
