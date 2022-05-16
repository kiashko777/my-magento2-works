<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Helper\Bootstrap.
 */

namespace Magento\Test\Helper;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Application;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\Bootstrap
     */
    protected $_object;

    /**
     * @var \Magento\TestFramework\Bootstrap|MockObject
     */
    protected $_bootstrap;

    /**
     * @var Application|MockObject
     */
    protected $_application;

    /**
     * Predefined application initialization parameters
     *
     * @var array
     */
    protected $_fixtureInitParams = [
        Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS => [
            DirectoryList::CONFIG => ['path' => __DIR__],
            DirectoryList::VAR_DIR => ['path' => __DIR__],
        ],
    ];

    /**
     */
    public function testGetInstanceEmptyProhibited()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Helper instance is not defined yet.');

        \Magento\TestFramework\Helper\Bootstrap::getInstance();
    }

    public function testSetInstanceFirstAllowed()
    {
        \Magento\TestFramework\Helper\Bootstrap::setInstance($this->_object);
        return $this->_object;
    }

    /**
     * @depends testSetInstanceFirstAllowed
     */
    public function testGetInstanceAllowed(\Magento\TestFramework\Helper\Bootstrap $expectedInstance)
    {
        $this->assertSame($expectedInstance, \Magento\TestFramework\Helper\Bootstrap::getInstance());
    }

    /**
     * @depends testSetInstanceFirstAllowed
     */
    public function testSetInstanceChangeProhibited()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Helper instance cannot be redefined.');

        \Magento\TestFramework\Helper\Bootstrap::setInstance($this->_object);
    }

    public function testCanTestHeaders()
    {
        if (!function_exists('xdebug_get_headers')) {
            $this->assertFalse(
                \Magento\TestFramework\Helper\Bootstrap::canTestHeaders(),
                'Expected inability to test headers.'
            );
            return;
        }
        $expectedHeader = 'SomeHeader: header-value';
        $expectedCookie = 'Set-Cookie: SomeCookie=cookie-value';

        /* Make sure that chosen reference samples are unique enough to rely on them */
        $actualHeaders = xdebug_get_headers();
        $this->assertNotContains($expectedHeader, $actualHeaders);
        $this->assertNotContains($expectedCookie, $actualHeaders);

        /* Determine whether header-related functions can be in fact called with no error */
        $expectedCanTest = true;
        set_error_handler(
            function () use (&$expectedCanTest) {
                $expectedCanTest = false;
            }
        );
        header($expectedHeader);
        setcookie('SomeCookie', 'cookie-value');
        restore_error_handler();

        $this->assertEquals($expectedCanTest, \Magento\TestFramework\Helper\Bootstrap::canTestHeaders());

        if ($expectedCanTest) {
            $actualHeaders = xdebug_get_headers();
            $this->assertContains($expectedHeader, $actualHeaders);
            $this->assertContains($expectedCookie, $actualHeaders);
        }
    }

    public function testGetAppTempDir()
    {
        $this->_application->expects($this->once())->method('getTempDir')->willReturn(__DIR__);
        $this->assertEquals(__DIR__, $this->_object->getAppTempDir());
    }

    public function testGetAppInitParams()
    {
        $this->_application->expects(
            $this->once()
        )->method(
            'getInitParams'
        )->willReturn(
            $this->_fixtureInitParams
        );
        $this->assertEquals($this->_fixtureInitParams, $this->_object->getAppInitParams());
    }

    public function testReinitialize()
    {
        $this->_application->expects($this->once())->method('reinitialize')->with($this->_fixtureInitParams);
        $this->_object->reinitialize($this->_fixtureInitParams);
    }

    public function testRunApp()
    {
        $this->_application->expects($this->once())->method('run');
        $this->_object->runApp();
    }

    protected function setUp(): void
    {
        $this->_application = $this->createPartialMock(
            Application::class,
            ['getTempDir', 'getInitParams', 'reinitialize', 'run']
        );
        $this->_bootstrap = $this->getMockBuilder(\Magento\TestFramework\Bootstrap::class)
            ->disableOriginalConstructor()
            ->addMethods(['getDbVendorName'])
            ->onlyMethods(['getApplication'])
            ->getMock();
        $this->_bootstrap->expects(
            $this->any()
        )->method(
            'getApplication'
        )->willReturn(
            $this->_application
        );
        $this->_object = new \Magento\TestFramework\Helper\Bootstrap($this->_bootstrap);
    }

    protected function tearDown(): void
    {
        $this->_application = null;
        $this->_bootstrap = null;
        $this->_object = null;
    }
}
