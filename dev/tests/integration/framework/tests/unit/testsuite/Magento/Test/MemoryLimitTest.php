<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test;

use LogicException;
use Magento\TestFramework\Helper\Memory;
use Magento\TestFramework\MemoryLimit;
use PHPUnit\Framework\TestCase;

class MemoryLimitTest extends TestCase
{
    public function testPrintHeader()
    {
        $result = MemoryLimit::printHeader();
        $this->assertNotEmpty($result);
        $this->assertStringEndsWith(PHP_EOL, $result);
    }

    public function testPrintStats()
    {
        $object = $this->_createObject(0, 0);
        $result = $object->printStats();
        $this->assertStringContainsString('Memory usage (OS):', $result);
        $this->assertStringContainsString('1.00M', $result);
        $this->assertStringContainsString('Estimated memory leak:', $result);
        $this->assertStringContainsString('reported by PHP', $result);
        $this->assertStringEndsWith(PHP_EOL, $result);

        $object = $this->_createObject('2M', 0);
        $this->assertStringContainsString('50.00% of configured 2.00M limit', $object->printStats());

        $object = $this->_createObject(0, '500K');
        $this->assertStringContainsString('% of configured 0.49M limit', $object->printStats());
    }

    /**
     * @param string $memCap
     * @param string $leakCap
     * @return MemoryLimit
     */
    protected function _createObject($memCap, $leakCap)
    {
        $helper = $this->createPartialMock(Memory::class, ['getRealMemoryUsage']);
        $helper->expects($this->any())->method('getRealMemoryUsage')->willReturn(1024 * 1024);
        return new MemoryLimit($memCap, $leakCap, $helper);
    }

    public function testValidateUsage()
    {
        $object = $this->_createObject(0, 0);
        $this->assertNull($object->validateUsage());
    }

    /**
     */
    public function testValidateUsageException()
    {
        $this->expectException(LogicException::class);

        $object = $this->_createObject('500K', '2M');
        $object->validateUsage();
    }
}
