<?php
/**
 * Test case for \Magento\Framework\Profiler\Driver\Standard\Output\Html
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Profiler\Driver\Standard\Output;

use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    /**
     * @var Html
     */
    protected $_output;

    /**
     * Test display method
     *
     * @dataProvider displayDataProvider
     * @param string $statFile
     * @param string $expectedHtmlFile
     */
    public function testDisplay($statFile, $expectedHtmlFile)
    {
        $stat = include $statFile;
        $expectedHtml = file_get_contents($expectedHtmlFile);

        ob_start();
        $this->_output->display($stat);
        $actualHtml = ob_get_clean();

        $this->_assertDisplayResultEquals($actualHtml, $expectedHtml);
    }

    /**
     * Asserts display() result equals
     *
     * @param string $actualHtml
     * @param string $expectedHtml
     */
    protected function _assertDisplayResultEquals($actualHtml, $expectedHtml)
    {
        $expectedHtml = ltrim(preg_replace('/^<!--.+?-->/s', '', $expectedHtml));
        if (preg_match('/Code Profiler \(Memory usage: real - (\d+), emalloc - (\d+)\)/', $actualHtml, $matches)) {
            list(, $realMemory, $emallocMemory) = $matches;
            $expectedHtml = str_replace(
                ['%real_memory%', '%emalloc_memory%'],
                [$realMemory, $emallocMemory],
                $expectedHtml
            );
        }
        $this->assertEquals($expectedHtml, $actualHtml);
    }

    /**
     * @return array
     */
    public function displayDataProvider()
    {
        return [
            ['statFile' => __DIR__ . '/_files/timers.php', 'expectedHtmlFile' => __DIR__ . '/_files/output.html']
        ];
    }

    protected function setUp(): void
    {
        $this->_output = new Html();
    }
}
