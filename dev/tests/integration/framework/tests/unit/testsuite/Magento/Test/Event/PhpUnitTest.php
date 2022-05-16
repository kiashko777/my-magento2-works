<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Event\PhpUnit.
 */

namespace Magento\Test\Event;

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Event\Magento;
use Magento\TestFramework\Event\PhpUnit;
use Magento\TestFramework\EventManager;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\DataProviderTestSuite;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;

class PhpUnitTest extends TestCase
{
    /**
     * @var PhpUnit
     */
    protected $_object;

    /**
     * @var EventManager|MockObject
     */
    protected $_eventManager;

    public function testConstructorDefaultEventManager()
    {
        PhpUnit::setDefaultEventManager($this->_eventManager);
        $this->_object = new PhpUnit();
        $this->testStartTestSuiteFireEvent();
    }

    public function testStartTestSuiteFireEvent()
    {
        $this->_eventManager->expects($this->once())->method('fireEvent')->with('startTestSuite');
        $this->_object->startTestSuite(new TestSuite());
    }

    /**
     */
    public function testConstructorException()
    {
        $this->expectException(LocalizedException::class);

        new Magento();
    }

    /**
     * @param string $method
     * @dataProvider doNotFireEventDataProvider
     */
    public function testDoNotFireEvent($method)
    {
        $this->_eventManager->expects($this->never())->method('fireEvent');
        $this->_object->{$method}($this, new AssertionFailedError(), 0);
    }

    public function doNotFireEventDataProvider()
    {
        return [
            'method "addError"' => ['addError'],
            'method "addFailure"' => ['addFailure'],
            'method "addIncompleteTest"' => ['addIncompleteTest'],
            'method "addSkippedTest"' => ['addSkippedTest']
        ];
    }

    public function testStartTestSuiteDoNotFireEvent()
    {
        $this->_eventManager->expects($this->never())->method('fireEvent');
        $this->_object->startTestSuite(new DataProviderTestSuite());
    }

    public function testEndTestSuiteFireEvent()
    {
        $this->_eventManager->expects($this->once())->method('fireEvent')->with('endTestSuite');
        $this->_object->endTestSuite(new TestSuite());
    }

    public function testEndTestSuiteDoNotFireEvent()
    {
        $this->_eventManager->expects($this->never())->method('fireEvent');
        $this->_object->endTestSuite(new DataProviderTestSuite());
    }

    public function testStartTestFireEvent()
    {
        $this->_eventManager->expects($this->once())->method('fireEvent')->with('startTest');
        $this->_object->startTest($this);
    }

    public function testStartTestDoNotFireEvent()
    {
        $this->_eventManager->expects($this->never())->method('fireEvent');
        //   $this->_object->startTest(new \PHPUnit\Framework\Warning());
        $this->_object->startTest($this->createMock(Test::class));
    }

    public function testEndTestFireEvent()
    {
        $this->_eventManager->expects($this->once())->method('fireEvent')->with('endTest');
        $this->_object->endTest($this, 0);
    }

    public function testEndTestDoNotFireEvent()
    {
        $this->_eventManager->expects($this->never())->method('fireEvent');
        //     $this->_object->endTest(new \PHPUnit\Framework\Warning(), 0);
        $this->_object->endTest($this->createMock(Test::class), 0);
    }

    protected function setUp(): void
    {
        $this->_eventManager = $this->getMockBuilder(EventManager::class)
            ->setMethods(['fireEvent'])
            ->setConstructorArgs([[]])
            ->getMock();
        $this->_object = new PhpUnit($this->_eventManager);
    }

    protected function tearDown(): void
    {
        PhpUnit::setDefaultEventManager(null);
    }
}
