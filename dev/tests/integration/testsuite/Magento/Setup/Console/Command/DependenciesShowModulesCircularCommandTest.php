<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Console\Command;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\DirSearch;
use Magento\Framework\View\Design\Theme\ThemePackageList;
use Magento\Setup\Model\ObjectManagerProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DependenciesShowModulesCircularCommandTest extends TestCase
{
    /**
     * @var DependenciesShowModulesCircularCommand
     */
    private $command;

    /**
     * @var CommandTester
     */
    private $commandTester;

    public function testExecute()
    {
        $this->commandTester->execute(
            ['--output' => __DIR__ . '/_files/output/circular.csv']
        );
        $this->assertEquals('Report successfully processed.' . PHP_EOL, $this->commandTester->getDisplay());
        $fileContents = file_get_contents(__DIR__ . '/_files/output/circular.csv');
        $this->assertStringContainsString(
            '"Circular dependencies:","Total number of chains"' . PHP_EOL . ',2' . PHP_EOL,
            $fileContents
        );
        $this->assertStringContainsString('"Circular dependencies for each module:",' . PHP_EOL, $fileContents);
        $this->assertStringContainsString(
            'magento/module-a,1' . PHP_EOL . 'magento/module-a->magento/module-b->magento/module-a' . PHP_EOL,
            $fileContents
        );
        $this->assertStringContainsString(
            'magento/module-b,1' . PHP_EOL . 'magento/module-b->magento/module-a->magento/module-b' . PHP_EOL,
            $fileContents
        );
    }

    protected function setUp(): void
    {
        $modules = [
            'Magento_A' => __DIR__ . '/_files/root/app/code/Magento/A',
            'Magento_B' => __DIR__ . '/_files/root/app/code/Magento/B'
        ];

        $objectManagerProvider = $this->createMock(ObjectManagerProvider::class);
        $objectManager = $this->createMock(ObjectManager::class);
        $objectManagerProvider->expects($this->once())->method('get')->willReturn($objectManager);

        $themePackageListMock = $this->createMock(ThemePackageList::class);
        $componentRegistrarMock = $this->createMock(ComponentRegistrar::class);
        $componentRegistrarMock->expects($this->any())->method('getPaths')->willReturn($modules);
        $dirSearchMock = $this->createMock(DirSearch::class);
        $objectManager->expects($this->any())->method('get')->willReturnMap([
            [ThemePackageList::class, $themePackageListMock],
            [ComponentRegistrar::class, $componentRegistrarMock],
            [DirSearch::class, $dirSearchMock]
        ]);

        $this->command = new DependenciesShowModulesCircularCommand($objectManagerProvider);
        $this->commandTester = new CommandTester($this->command);
    }

    protected function tearDown(): void
    {
        if (file_exists(__DIR__ . '/_files/output/circular.csv')) {
            unlink(__DIR__ . '/_files/output/circular.csv');
        }
    }
}
