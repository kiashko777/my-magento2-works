<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Developer\Model\Logger\Handler;

use Exception;
use Magento\Config\Setup\ConfigOptionsList;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\Shell;
use Magento\Setup\Mvc\Bootstrap\InitParamListener;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @magentoAppIsolation enabled
 */
class DebugTest extends TestCase
{
    /**
     * @var string
     */
    private static $backupFile = 'env.base.php';
    /**
     * @var string
     */
    private static $configFile = 'env.php';
    /**
     * @var Monolog
     */
    private $logger;
    /**
     * @var WriteInterface
     */
    private $etcDirectory;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var Shell
     */
    private $shell;
    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;
    /**
     * @var string
     */
    private $debugLogPath = '';
    /**
     * @var Debug
     */
    private $debugHandler;

    /**
     * @throws LocalizedException
     */
    public function testDebugInProductionMode()
    {
        $message = 'test message';
        $this->reinitDebugHandler(State::MODE_PRODUCTION);

        $this->removeDebugLog();
        $this->logger->debug($message);
        $this->assertFileDoesNotExist($this->getDebuggerLogPath());
        $this->assertNull($this->deploymentConfig->get(ConfigOptionsList::CONFIG_PATH_DEBUG_LOGGING));

        $this->checkCommonFlow($message);
        $this->reinitDeploymentConfig();
    }

    /**
     * @param string $instanceMode
     * @throws LocalizedException
     */
    private function reinitDebugHandler(string $instanceMode)
    {
        $this->debugHandler = $this->objectManager->create(
            Debug::class,
            [
                'filePath' => Bootstrap::getInstance()->getAppTempDir(),
                'state' => $this->objectManager->create(
                    State::class,
                    [
                        'mode' => $instanceMode,
                    ]
                ),
            ]
        );
        $this->logger->setHandlers(
            [
                $this->debugHandler,
            ]
        );
    }

    /**
     * @return void
     */
    private function removeDebugLog()
    {
        $this->detachLogger();
        if (file_exists($this->getDebuggerLogPath())) {
            unlink($this->getDebuggerLogPath());
        }
    }

    /**
     * @return void
     */
    private function detachLogger()
    {
        $this->debugHandler->close();
    }

    /**
     * @return string
     */
    private function getDebuggerLogPath()
    {
        if (!$this->debugLogPath) {
            foreach ($this->logger->getHandlers() as $handler) {
                if ($handler instanceof Debug) {
                    $this->debugLogPath = $handler->getUrl();
                }
            }
        }

        return $this->debugLogPath;
    }

    /**
     * @param string $message
     * @throws LocalizedException
     */
    private function checkCommonFlow(string $message)
    {
        $this->enableDebugging(true);
        $this->removeDebugLog();
        $this->logger->debug($message);
        $this->assertFileExists($this->getDebuggerLogPath());
        $this->assertStringContainsString($message, file_get_contents($this->getDebuggerLogPath()));

        $this->enableDebugging(false);
        $this->removeDebugLog();
        $this->logger->debug($message);
        $this->assertFileDoesNotExist($this->getDebuggerLogPath());
    }

    /**
     * @param bool $flag
     * @throws LocalizedException
     */
    private function enableDebugging(bool $flag)
    {
        $this->shell->execute(
            PHP_BINARY . ' -f %s setup:config:set -n --%s=%s --%s=%s',
            [
                BP . '/bin/magento',
                ConfigOptionsList::INPUT_KEY_DEBUG_LOGGING,
                (int)$flag,
                InitParamListener::BOOTSTRAP_PARAM,
                urldecode(http_build_query(Bootstrap::getInstance()->getAppInitParams())),
            ]
        );
        $this->deploymentConfig->resetData();
        $this->assertSame((int)$flag, $this->deploymentConfig->get(ConfigOptionsList::CONFIG_PATH_DEBUG_LOGGING));
    }

    /**
     * @throws LocalizedException
     */
    public function testDebugInDeveloperMode()
    {
        $message = 'test message';
        $this->reinitDebugHandler(State::MODE_DEVELOPER);
        $this->deploymentConfig->resetData();
        $this->removeDebugLog();
        $this->logger->debug($message);
        $this->assertFileExists($this->getDebuggerLogPath());
        $this->assertStringContainsString($message, file_get_contents($this->getDebuggerLogPath()));
        $this->assertNull($this->deploymentConfig->get(ConfigOptionsList::CONFIG_PATH_DEBUG_LOGGING));

        $this->checkCommonFlow($message);
        $this->reinitDeploymentConfig();
    }

    /**
     * @inheritdoc
     * @throws FileSystemException
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->shell = $this->objectManager->get(Shell::class);
        $this->logger = $this->objectManager->get(LoggerInterface::class);
        $this->deploymentConfig = $this->objectManager->get(DeploymentConfig::class);

        /** @var Filesystem $filesystem */
        $filesystem = $this->objectManager->create(Filesystem::class);
        $this->etcDirectory = $filesystem->getDirectoryWrite(DirectoryList::CONFIG);
        $this->etcDirectory->copyFile(self::$configFile, self::$backupFile);
    }

    /**
     * @inheritdoc
     * @throws FileSystemException
     */
    protected function tearDown(): void
    {
        $this->reinitDeploymentConfig();
        $this->etcDirectory->delete(self::$backupFile);
    }

    /**
     * @throws FileSystemException
     */
    private function reinitDeploymentConfig()
    {
        $this->etcDirectory->delete(self::$configFile);
        $this->etcDirectory->copyFile(self::$backupFile, self::$configFile);
        $this->deploymentConfig->resetData();
    }
}
