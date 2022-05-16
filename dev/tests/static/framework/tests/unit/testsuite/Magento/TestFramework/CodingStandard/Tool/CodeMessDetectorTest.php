<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\CodingStandard\Tool;

use PHPMD\TextUI\Command;
use PHPUnit\Framework\TestCase;

class CodeMessDetectorTest extends TestCase
{
    public function testCanRun()
    {
        $messDetector = new CodeMessDetector(
            'some/ruleset/file.xml',
            'some/report/file.xml'
        );

        $this->assertEquals(
            class_exists(Command::class),
            $messDetector->canRun()
        );
    }
}
