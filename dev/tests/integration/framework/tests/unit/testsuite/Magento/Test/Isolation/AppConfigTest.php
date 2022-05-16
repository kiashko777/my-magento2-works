<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\TestFramework\Isolation\WorkingDirectory.
 */

namespace Magento\Test\Isolation;

use Magento\TestFramework\App\Config;
use Magento\TestFramework\Isolation\AppConfig;
use Magento\TestFramework\Isolation\WorkingDirectory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AppConfigTest extends TestCase
{
    /**
     * @var WorkingDirectory
     */
    private $model;

    public function testStartTestEndTest()
    {
        $test = $this->getMockBuilder(TestCase::class)
            ->disableOriginalConstructor()
            ->getMock();
        $modelReflection = new ReflectionClass($this->model);
        $testAppConfigProperty = $modelReflection->getProperty('testAppConfig');
        $testAppConfigProperty->setAccessible(true);
        $testAppConfigMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $testAppConfigProperty->setValue($this->model, $testAppConfigMock);
        $testAppConfigMock->expects($this->once())
            ->method('clean');
        $this->model->startTest($test);
    }

    protected function setUp(): void
    {
        $this->model = new AppConfig();
    }

    protected function tearDown(): void
    {
        $this->model = null;
    }
}
