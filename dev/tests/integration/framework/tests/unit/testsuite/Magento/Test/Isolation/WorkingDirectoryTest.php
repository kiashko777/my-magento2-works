<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Isolation\WorkingDirectory.
 */

namespace Magento\Test\Isolation;

use Magento\TestFramework\Isolation\WorkingDirectory;
use PHPUnit\Framework\TestCase;

class WorkingDirectoryTest extends TestCase
{
    /**
     * @var WorkingDirectory
     */
    protected $_object;

    public function testStartTestEndTest()
    {
        $oldWorkingDir = getcwd();
        $newWorkingDir = __DIR__;
        if ($oldWorkingDir == $newWorkingDir) {
            $this->markTestSkipped("Test requires the current working directory to differ from '{$oldWorkingDir}'.");
        }
        $this->_object->startTest($this);
        chdir($newWorkingDir);
        $this->assertEquals($newWorkingDir, getcwd(), 'Unable to change the current working directory.');
        $this->_object->endTest($this);
        $this->assertEquals($oldWorkingDir, getcwd(), 'Current working directory was not restored.');
    }

    protected function setUp(): void
    {
        $this->_object = new WorkingDirectory();
    }

    protected function tearDown(): void
    {
        $this->_object = null;
    }
}
