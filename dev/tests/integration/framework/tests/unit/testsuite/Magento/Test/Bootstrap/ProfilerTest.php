<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Bootstrap\Profiler.
 */

namespace Magento\Test\Bootstrap;

use Magento\Framework\Profiler\Driver\Standard;
use Magento\Framework\Profiler\Driver\Standard\Output\Csvfile;
use Magento\TestFramework\Bootstrap\Profiler;
use Magento\TestFramework\Profiler\OutputBamboo;
use PHPUnit\Framework\TestCase;

class ProfilerTest extends TestCase
{
    /**
     * @var Profiler
     */
    protected $_object;

    /**
     * @var Standard|PHPUnit\Framework\MockObject_MockObject
     */
    protected $_driver;

    public function testRegisterFileProfiler()
    {
        $this->_driver->expects(
            $this->once()
        )->method(
            'registerOutput'
        )->with(
            $this->isInstanceOf(Csvfile::class)
        );
        $this->_object->registerFileProfiler('php://output');
    }

    public function testRegisterBambooProfiler()
    {
        $this->_driver->expects(
            $this->once()
        )->method(
            'registerOutput'
        )->with(
            $this->isInstanceOf(OutputBamboo::class)
        );
        $this->_object->registerBambooProfiler('php://output', __DIR__ . '/_files/metrics.php');
    }

    protected function setUp(): void
    {
        $this->expectOutputString('');
        $this->_driver =
            $this->createPartialMock(Standard::class, ['registerOutput']);
        $this->_object = new Profiler($this->_driver);
    }

    protected function tearDown(): void
    {
        $this->_driver = null;
        $this->_object = null;
    }
}
