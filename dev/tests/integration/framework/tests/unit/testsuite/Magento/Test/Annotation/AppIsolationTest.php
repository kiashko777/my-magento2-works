<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Annotation\AppIsolation.
 */

namespace Magento\Test\Annotation;

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Annotation\AppIsolation;
use Magento\TestFramework\Application;
use Magento\TestFramework\TestCase\AbstractController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AppIsolationTest extends TestCase
{
    /**
     * @var AppIsolation
     */
    protected $_object;

    /**
     * @var MockObject
     */
    protected $_application;

    public function testStartTestSuite()
    {
        $this->_application->expects($this->once())->method('reinitialize');
        $this->_object->startTestSuite();
    }

    /**
     * @magentoAppIsolation invalid
     */
    public function testEndTestIsolationInvalid()
    {
        $this->expectException(LocalizedException::class);

        $this->_object->endTest($this);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppIsolation disabled
     */
    public function testEndTestIsolationAmbiguous()
    {
        $this->expectException(LocalizedException::class);

        $this->_object->endTest($this);
    }

    public function testEndTestIsolationDefault()
    {
        $this->_application->expects($this->never())->method('reinitialize');
        $this->_object->endTest($this);
    }

    public function testEndTestIsolationController()
    {
        /** @var $controllerTest AbstractController */
        $controllerTest = $this->getMockForAbstractClass(AbstractController::class);
        $this->_application->expects($this->once())->method('reinitialize');
        $this->_object->endTest($controllerTest);
    }

    /**
     * @magentoAppIsolation disabled
     */
    public function testEndTestIsolationDisabled()
    {
        $this->_application->expects($this->never())->method('reinitialize');
        $this->_object->endTest($this);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testEndTestIsolationEnabled()
    {
        $this->_application->expects($this->once())->method('reinitialize');
        $this->_object->endTest($this);
    }

    protected function setUp(): void
    {
        $this->_application = $this->createPartialMock(Application::class, ['reinitialize']);
        $this->_object = new AppIsolation($this->_application);
    }

    protected function tearDown(): void
    {
        $this->_application = null;
        $this->_object = null;
    }
}
