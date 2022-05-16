<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\EventManager.
 */

namespace Magento\Test;

use Magento\TestFramework\EventManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class EventManagerTest extends TestCase
{
    /**
     * @var EventManager
     */
    protected $_eventManager;

    /**
     * @var MockObject
     */
    protected $_subscriberOne;

    /**
     * @var MockObject
     */
    protected $_subscriberTwo;

    /**
     * @param bool $reverseOrder
     * @param array $expectedSubscribers
     * @dataProvider fireEventDataProvider
     */
    public function testFireEvent($reverseOrder, $expectedSubscribers)
    {
        $actualSubscribers = [];
        $callback = function () use (&$actualSubscribers) {
            $actualSubscribers[] = 'subscriberOne';
        };
        $this->_subscriberOne->expects($this->once())->method('testEvent')->willReturnCallback($callback);
        $callback = function () use (&$actualSubscribers) {
            $actualSubscribers[] = 'subscriberTwo';
        };
        $this->_subscriberTwo->expects($this->once())->method('testEvent')->willReturnCallback($callback);
        $this->_eventManager->fireEvent('testEvent', [], $reverseOrder);
        $this->assertEquals($expectedSubscribers, $actualSubscribers);
    }

    public function fireEventDataProvider()
    {
        return [
            'straight order' => [false, ['subscriberOne', 'subscriberTwo']],
            'reverse order' => [true, ['subscriberTwo', 'subscriberOne']]
        ];
    }

    public function testFireEventParameters()
    {
        $paramOne = 123;
        $paramTwo = 456;
        $this->_subscriberOne->expects($this->once())->method('testEvent')->with($paramOne, $paramTwo);
        $this->_subscriberTwo->expects($this->once())->method('testEvent')->with($paramOne, $paramTwo);
        $this->_eventManager->fireEvent('testEvent', [$paramOne, $paramTwo]);
    }

    protected function setUp(): void
    {
        $this->_subscriberOne = $this->getMockBuilder(stdClass::class)
            ->addMethods(['testEvent'])
            ->getMock();
        $this->_subscriberTwo = $this->getMockBuilder(stdClass::class)
            ->addMethods(['testEvent'])
            ->getMock();
        $this->_eventManager = new EventManager(
            [$this->_subscriberOne, $this->_subscriberTwo]
        );
    }
}
