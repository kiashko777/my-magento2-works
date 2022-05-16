<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SampleData\Model;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\Config\Composer\PackageFactory;
use Magento\Framework\Filesystem;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DependencyTest extends TestCase
{
    /**
     * @var Dependency
     */
    private $model;

    /**
     * @var ComposerInformation|MockObject
     */
    private $composerInformationMock;

    /**
     * @var ComponentRegistrar|MockObject
     */
    private $componentRegistrarMock;

    public function testGetSampleDataPackages()
    {
        $this->composerInformationMock->expects($this->once())
            ->method('getSuggestedPackages')
            ->willReturn([]);
        $this->componentRegistrarMock->expects($this->once())
            ->method('getPaths')
            ->with(ComponentRegistrar::MODULE)
            ->willReturn([
                __DIR__ . '/../_files/Modules/FirstModule',
                __DIR__ . '/../_files/Modules/SecondModule',
                __DIR__ . '/../_files/Modules/ThirdModule',
                __DIR__ . '/../_files/Modules/FourthModule'
            ]);

        $this->assertSame(
            ['magento/module-first-sample-data' => '777.7.*'],
            $this->model->getSampleDataPackages()
        );
    }

    protected function setUp(): void
    {
        $this->composerInformationMock = $this->getMockBuilder(ComposerInformation::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
        $this->componentRegistrarMock = $this->getMockBuilder(ComponentRegistrar::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->create(
            Dependency::class,
            [
                'composerInformation' => $this->composerInformationMock,
                'filesystem' => $objectManager->get(Filesystem::class),
                'packageFactory' => $objectManager->get(PackageFactory::class),
                'componentRegistrar' => $this->componentRegistrarMock
            ]
        );
    }
}
