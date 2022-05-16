<?php
/**
 * Test case for \Magento\Framework\Profiler\Driver\Standard\Output\Csvfile
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Profiler\Driver\Standard\Output;

use PHPUnit\Framework\TestCase;

class CsvfileTest extends TestCase
{
    /**
     * @var Csvfile
     */
    protected $_output;

    /**
     * @var string
     */
    protected $_outputFile;

    /**
     * Test display method
     *
     * @dataProvider displayDataProvider
     * @param string $statFile
     * @param string $expectedFile
     * @param string $delimiter
     * @param string $enclosure
     */
    public function testDisplay($statFile, $expectedFile, $delimiter = ',', $enclosure = '"')
    {
        $this->_output = new Csvfile(
            ['filePath' => $this->_outputFile, 'delimiter' => $delimiter, 'enclosure' => $enclosure]
        );
        $stat = include $statFile;
        $this->_output->display($stat);
        $this->assertFileEquals($expectedFile, $this->_outputFile);
    }

    /**
     * @return array
     */
    public function displayDataProvider()
    {
        return [
            'Default delimiter & enclosure' => [
                'statFile' => __DIR__ . '/_files/timers.php',
                'expectedHtmlFile' => __DIR__ . '/_files/output_default.csv',
            ],
            'Custom delimiter & enclosure' => [
                'statFile' => __DIR__ . '/_files/timers.php',
                'expectedHtmlFile' => __DIR__ . '/_files/output_custom.csv',
                '.',
                '`',
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->_outputFile = tempnam(sys_get_temp_dir(), __CLASS__);
    }
}
