<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Event\Magento.
 */

namespace Magento\Test\Event;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Event\Magento;
use Magento\TestFramework\EventManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class MagentoTest extends TestCase
{
    /**
     * @var Magento
     */
    protected $_object;

    /**
     * @var EventManager|MockObject
     */
    protected $_eventManager;

    public function testConstructorDefaultEventManager()
    {
        Magento::setDefaultEventManager($this->_eventManager);
        $this->_object = new Magento();
        $this->testInitStoreAfter();
    }

    public function testInitStoreAfter()
    {
        $this->_eventManager->expects($this->once())->method('fireEvent')->with('initStoreAfter');
        $this->_object->execute($this->createMock(Observer::class));
    }

    /**
     * @dataProvider constructorExceptionDataProvider
     * @param mixed $eventManager
     */
    public function testConstructorException($eventManager)
    {
        $this->expectException(LocalizedException::class);

        new Magento($eventManager);
    }

    public function constructorExceptionDataProvider()
    {
        return ['no event manager' => [null], 'not an event manager' => [new stdClass()]];
    }

    protected function setUp(): void
    {
        $this->_eventManager = $this->getMockBuilder(EventManager::class)
            ->setMethods(['fireEvent'])
            ->setConstructorArgs([[]])
            ->getMock();
        $this->_object = new Magento($this->_eventManager);
    }

    protected function tearDown(): void
    {
        Magento::setDefaultEventManager(null);
    }
}
