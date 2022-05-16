<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Bootstrap of the application profiler
 */

namespace Magento\TestFramework\Bootstrap;

use Magento\Framework\Profiler\Driver\Standard;
use Magento\Framework\Profiler\Driver\Standard\Output\Csvfile;
use Magento\TestFramework\Profiler\OutputBamboo;

class Profiler
{
    /**
     * Profiler driver instance
     *
     * @var Standard
     */
    protected $_driver;

    /**
     * Whether a profiler driver has been already registered or not
     *
     * @var bool
     */
    protected $_isDriverRegistered = false;

    /**
     * Constructor
     *
     * @param Standard $driver
     */
    public function __construct(Standard $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Register file-based profiling
     *
     * @param string $profilerOutputFile
     */
    public function registerFileProfiler($profilerOutputFile)
    {
        $this->_registerDriver();
        $this->_driver->registerOutput(
            new Csvfile(['filePath' => $profilerOutputFile])
        );
    }

    /**
     * Register profiler driver to involve it into the results processing
     */
    protected function _registerDriver()
    {
        if (!$this->_isDriverRegistered) {
            $this->_isDriverRegistered = true;
            \Magento\Framework\Profiler::add($this->_driver);
        }
    }

    /**
     * Register profiler with Bamboo-friendly output format
     *
     * @param string $profilerOutputFile
     * @param string $profilerMetricsFile
     */
    public function registerBambooProfiler($profilerOutputFile, $profilerMetricsFile)
    {
        $this->_registerDriver();
        $this->_driver->registerOutput(
            new OutputBamboo(
                ['filePath' => $profilerOutputFile, 'metrics' => require $profilerMetricsFile]
            )
        );
    }
}
