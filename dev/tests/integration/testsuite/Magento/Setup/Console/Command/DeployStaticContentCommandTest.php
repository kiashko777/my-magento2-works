<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Console\Command;

use Magento\Deploy\Console\ConsoleLogger;
use Magento\Deploy\Console\ConsoleLoggerFactory;
use Magento\Deploy\Console\DeployStaticOptions;
use Magento\Framework\App\DeploymentConfig\FileReader;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Setup\Model\ObjectManagerProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeployStaticContentCommandTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ConfigFilePool
     */
    private $configFilePool;

    /**
     * @var FileReader
     */
    private $reader;

    /**
     * @var Writer
     */
    private $writer;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $envConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function testDeployStaticWithoutDbConnection()
    {
        // emulate app:config:dump command
        $newData = array_replace_recursive(
            $this->config,
            require __DIR__ . '/_files/config/dump_config.php'
        );
        $this->writer->saveConfig([ConfigFilePool::APP_CONFIG => $newData], true);

        // remove application environment config for emulate work without db
        $this->filesystem->getDirectoryWrite(DirectoryList::CONFIG)->writeFile(
            $this->configFilePool->getPath(ConfigFilePool::APP_ENV),
            "<?php\n return [];\n"
        );
        $this->storeManager->reinitStores();

        $this->commandTester = new CommandTester($this->getStaticContentDeployCommand());
        $this->commandTester->execute(
            ['--force' => 1, '--' . DeployStaticOptions::THEME => ['Magento/blank']]
        );
        $commandOutput = $this->commandTester->getDisplay();

        $this->assertEquals(Cli::RETURN_SUCCESS, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('Execution time:', $commandOutput);
        $this->assertStringContainsString('frontend/Magento/blank/en_US', $commandOutput);
        $this->assertStringNotContainsString('frontend/Magento/luma/en_US', $commandOutput);
        $this->assertStringNotContainsString('Adminhtml/Magento/backend', $commandOutput);
    }

    /**
     * Create DeployStaticContentCommand instance with mocked loggers
     *
     * @return DeployStaticContentCommand
     */
    private function getStaticContentDeployCommand()
    {
        $objectManager = Bootstrap::getObjectManager();
        $consoleLoggerFactoryMock = $this->getMockBuilder(ConsoleLoggerFactory::class)
            ->setMethods(['getLogger'])
            ->disableOriginalConstructor()
            ->getMock();
        $consoleLoggerFactoryMock
            ->method('getLogger')
            ->willReturnCallback(
                function ($output) use ($objectManager) {
                    return $objectManager->create(ConsoleLogger::class, ['output' => $output]);
                }
            );
        $objectManagerProviderMock = $this->getMockBuilder(ObjectManagerProvider::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $objectManagerProviderMock
            ->method('get')
            ->willReturn(Bootstrap::getObjectManager());
        $deployStaticContentCommand = $objectManager->create(
            DeployStaticContentCommand::class,
            [
                'consoleLoggerFactory' => $consoleLoggerFactoryMock,
                'objectManagerProvider' => $objectManagerProviderMock
            ]
        );

        return $deployStaticContentCommand;
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->reader = $this->objectManager->get(FileReader::class);
        $this->writer = $this->objectManager->get(Writer::class);
        $this->filesystem = $this->objectManager->get(Filesystem::class);
        $this->configFilePool = $this->objectManager->get(ConfigFilePool::class);
        $this->storeManager = $this->objectManager->get(StoreManagerInterface::class);

        $this->config = $this->loadConfig();
        $this->envConfig = $this->loadEnvConfig();

        $this->clearStaticFiles();
    }

    /**
     * @return array
     */
    private function loadConfig()
    {
        return $this->reader->load(ConfigFilePool::APP_CONFIG);
    }

    /**
     * @return array
     */
    private function loadEnvConfig()
    {
        return $this->reader->load(ConfigFilePool::APP_ENV);
    }

    /**
     * Clear pub/static and var/view_preprocessed directories
     *
     * @return void
     */
    private function clearStaticFiles()
    {
        $this->filesystem->getDirectoryWrite(DirectoryList::PUB)->delete(DirectoryList::STATIC_VIEW);
        $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->delete(DirectoryList::TMP_MATERIALIZATION_DIR);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->filesystem->getDirectoryWrite(DirectoryList::CONFIG)->writeFile(
            $this->configFilePool->getPath(ConfigFilePool::APP_CONFIG),
            "<?php\n return [];\n"
        );
        $this->filesystem->getDirectoryWrite(DirectoryList::CONFIG)->writeFile(
            $this->configFilePool->getPath(ConfigFilePool::APP_ENV),
            "<?php\n return [];\n"
        );

        $this->writer->saveConfig([ConfigFilePool::APP_CONFIG => $this->config]);
        $this->writer->saveConfig([ConfigFilePool::APP_ENV => $this->envConfig]);

        $this->clearStaticFiles();
        $this->storeManager->reinitStores();
    }
}
