<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\CodingStandard\Tool;

use Magento\TestFramework\CodingStandard\Tool\CodeSniffer\Wrapper;
use PHP_CodeSniffer\Runner;
use PHPUnit\Framework\TestCase;

/**
 * Unit test to check CodeSniffer tool.
 */
class CodeSnifferTest extends TestCase
{
    /**
     * Rule set directory
     */
    const RULE_SET = 'some/ruleset/directory';
    /**
     * Report file
     */
    const REPORT_FILE = 'some/report/file.xml';
    /**
     * @var CodeSniffer
     */
    protected $_tool;
    /**
     * @var Runner
     */
    protected $_wrapper;

    public function testRun()
    {
        $whiteList = ['test' . rand(), 'test' . rand()];
        $extensions = ['test' . rand(), 'test' . rand()];

        $expectedCliEmulation = [
            'files' => $whiteList,
            'standards' => [self::RULE_SET],
            'extensions' => $extensions,
            'reports' => ['full' => self::REPORT_FILE],
        ];

        $this->_tool->setExtensions($extensions);

        $this->_wrapper->expects($this->once())
            ->method('setSettings')
            ->with($this->equalTo($expectedCliEmulation));

        $this->_wrapper->expects($this->once())
            ->method('runPHPCS');

        $this->_tool->run($whiteList);
    }

    protected function setUp(): void
    {
        $this->_wrapper = $this->createMock(Wrapper::class);
        $this->_tool = new CodeSniffer(
            self::RULE_SET,
            self::REPORT_FILE,
            $this->_wrapper
        );
    }
}
