<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Isolation of the current working directory changes between tests
 */

namespace Magento\TestFramework\Isolation;

use PHPUnit\Framework\TestCase;

class WorkingDirectory
{
    /**
     * @var string
     */
    private $_currentWorkingDir;

    /**
     * Handler for 'endTest' event
     *
     * @param TestCase $test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function startTest(TestCase $test)
    {
        $this->_currentWorkingDir = getcwd();
    }

    /**
     * Handler for 'startTest' event
     *
     * @param TestCase $test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(TestCase $test)
    {
        if (getcwd() != $this->_currentWorkingDir) {
            chdir($this->_currentWorkingDir);
        }
    }
}
