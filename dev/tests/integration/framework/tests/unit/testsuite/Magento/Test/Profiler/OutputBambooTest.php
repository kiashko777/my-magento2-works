<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Profiler;

use Magento\Framework\Profiler\Driver\Standard\Stat;
use Magento\TestFramework\Profiler\OutputBamboo;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\TestFramework\Profiler\OutputBamboo.
 */
require_once __DIR__ . '/OutputBambooTestFilter.php';

class OutputBambooTest extends TestCase
{
    /**
     * @var OutputBamboo
     */
    protected $_output;

    public static function setUpBeforeClass(): void
    {
        stream_filter_register('dataCollectorFilter', OutputBambooTestFilter::class);
    }

    public function testDisplay()
    {
        $this->_output->display(new Stat());
        OutputBambooTestFilter::assertCollectedData("Timestamp,\"sample metric (ms)\"\n%d,%d");
    }

    /**
     * Reset collected data and prescribe to pass stream data through the collector filter
     */
    protected function setUp(): void
    {
        OutputBambooTestFilter::resetCollectedData();

        /**
         * @link http://php.net/manual/en/wrappers.php.php
         */
        $this->_output = new OutputBamboo(
            [
                'filePath' => 'php://filter/write=dataCollectorFilter/resource=php://memory',
                'metrics' => ['sample metric (ms)' => ['profiler_key_for_sample_metric']],
            ]
        );
    }
}
