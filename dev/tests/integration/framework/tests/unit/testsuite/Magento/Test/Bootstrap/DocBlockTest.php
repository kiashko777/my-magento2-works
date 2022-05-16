<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Bootstrap\DocBlock.
 */

namespace Magento\Test\Bootstrap;

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Application;
use Magento\TestFramework\Bootstrap\DocBlock;
use Magento\TestFramework\Event\Magento;
use Magento\TestFramework\Event\PhpUnit;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DocBlockTest extends TestCase
{
    /**
     * @var DocBlock
     */
    protected $_object;

    /**
     * @var Application|MockObject
     */
    protected $_application;

    public function testRegisterAnnotations()
    {
        $this->_expectNoListenerCreation(
            PhpUnit::class,
            'Instance of the event manager is required.'
        );
        $this->_expectNoListenerCreation(
            Magento::class,
            'Instance of the "Magento\TestFramework\EventManager" is expected.'
        );
        $this->_object->registerAnnotations($this->_application);
        new PhpUnit();
        new Magento();
    }

    /**
     * Setup expectation of inability to instantiate an event listener without passing the event manager instance
     *
     * @param string $listenerClass
     * @param string $expectedExceptionMsg
     */
    protected function _expectNoListenerCreation($listenerClass, $expectedExceptionMsg)
    {
        try {
            new $listenerClass();
            $this->fail("Inability to instantiate the event listener '{$listenerClass}' is expected.");
        } catch (LocalizedException $e) {
            $this->assertEquals($expectedExceptionMsg, $e->getMessage());
        }
    }

    protected function setUp(): void
    {
        $this->_object = new DocBlock(__DIR__);
        $this->_application = $this->createMock(Application::class);
    }

    protected function tearDown(): void
    {
        $this->_object = null;
        $this->_application = null;
    }
}
