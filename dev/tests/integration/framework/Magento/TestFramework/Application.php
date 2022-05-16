<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework;

use DomainException;
use Magento\Eav\Model\Cache\Type;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Cache\Type\Layout;
use Magento\Framework\App\Cache\Type\Translate;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Http;
use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\State;
use Magento\Framework\Autoload\AutoloaderInterface;
use Magento\Framework\Autoload\Populator;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Config\Scope;
use Magento\Framework\Data\Form;
use Magento\Framework\Event\Config\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\Glob;
use Magento\Framework\Logger\Handler\Debug;
use Magento\Framework\Logger\Handler\Exception;
use Magento\Framework\Logger\Handler\System;
use Magento\Framework\Mail;
use Magento\Framework\ObjectManager\DynamicConfigInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Phrase\Renderer\Placeholder;
use Magento\Framework\Shell;
use Magento\SalesSequence\Model\Builder;
use Magento\TestFramework;
use Magento\TestFramework\Db\Sequence;
use Psr\Log\LoggerInterface;

/**
 * Encapsulates application installation, initialization and uninstall.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Application
{
    /**
     * Default application area.
     */
    const DEFAULT_APP_AREA = 'global';

    /**
     * DB vendor adapter instance.
     *
     * @var TestFramework\Db\AbstractDb
     */
    protected $_db;

    /**
     * Shell command executor.
     *
     * @var Shell
     */
    protected $_shell;
    /**
     * The loaded installation parameters.
     *
     * @var array
     */
    protected $installConfig;
    /**
     * Application *.xml configuration files.
     *
     * @var array
     */
    protected $_globalConfigDir;
    /**
     * Installation destination directory.
     *
     * @var string
     */
    protected $installDir;
    /**
     * Installation destination directory with configuration files.
     *
     * @var string
     */
    protected $_configDir;
    /**
     * Application initialization parameters.
     *
     * @var array
     */
    protected $_initParams = [];
    /**
     * Mode to run application.
     *
     * @var string
     */
    protected $_appMode;
    /**
     * Application area.
     *
     * @var null
     */
    protected $_appArea = null;
    /**
     * Primary DI Config.
     *
     * @var array
     */
    protected $_primaryConfigData = [];
    /**
     * Object manager factory.
     *
     * @var TestFramework\ObjectManagerFactory
     */
    protected $_factory;
    /**
     * Directory list.
     *
     * @var DirectoryList
     */
    protected $dirList;
    /**
     * @var bool
     */
    protected $dumpDb = true;
    /**
     * @var bool
     */
    protected $canLoadArea = true;
    /**
     * @var bool
     */
    protected $canInstallSequence = true;
    /**
     * Configuration file that contains installation parameters.
     *
     * @var string
     */
    private $installConfigFile;
    /**
     * Configuration file that contains array of post-installation commands to run through bin/magento CLI tool.
     *
     * @var string|null
     */
    private $postInstallSetupCommandsFile;
    /**
     * The loaded post-installation commands.
     *
     * @var array
     */
    private $postInstallSetupCommands;
    /**
     * Config file for integration tests.
     *
     * @var string
     */
    private $globalConfigFile;
    /**
     * Defines whether load test extension attributes or not.
     *
     * @var bool
     */
    private $loadTestExtensionAttributes;

    /**
     * Constructor.
     *
     * @param Shell $shell
     * @param string $installDir
     * @param string $installConfigFile
     * @param string $globalConfigFile
     * @param string $globalConfigDir
     * @param string $appMode
     * @param AutoloaderInterface $autoloadWrapper
     * @param bool|null $loadTestExtensionAttributes
     * @param string|null $postInstallSetupCommandsFile
     */
    public function __construct(
        Shell $shell,
                                 $installDir,
                                 $installConfigFile,
                                 $globalConfigFile,
                                 $globalConfigDir,
                                 $appMode,
        AutoloaderInterface      $autoloadWrapper,
                                 $loadTestExtensionAttributes = false,
                                 $postInstallSetupCommandsFile = null
    )
    {
        if (getcwd() != BP . '/dev/tests/integration') {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            chdir(BP . '/dev/tests/integration');
        }
        $this->_shell = $shell;
        $this->installConfigFile = $installConfigFile;
        $this->postInstallSetupCommandsFile = $postInstallSetupCommandsFile;
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $this->_globalConfigDir = realpath($globalConfigDir);
        $this->_appMode = $appMode;
        $this->installDir = $installDir;
        $this->loadTestExtensionAttributes = $loadTestExtensionAttributes;

        $customDirs = $this->getCustomDirs();
        $this->dirList = new DirectoryList(BP, $customDirs);
        Populator::populateMappings(
            $autoloadWrapper,
            $this->dirList
        );
        $this->_initParams = [
            \Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS => $customDirs,
            State::PARAM_MODE => $appMode
        ];
        $driverPool = new DriverPool();
        $configFilePool = new ConfigFilePool();
        $this->_factory = new TestFramework\ObjectManagerFactory($this->dirList, $driverPool, $configFilePool);

        $this->_configDir = $this->dirList->getPath(DirectoryList::CONFIG);
        $this->globalConfigFile = $globalConfigFile;
    }

    /**
     * Gets customized directory paths
     *
     * @return array
     */
    protected function getCustomDirs()
    {
        $path = DirectoryList::PATH;
        $var = "{$this->installDir}/var";
        $generated = "{$this->installDir}/generated";
        $customDirs = [
            DirectoryList::CONFIG => [$path => "{$this->installDir}/etc"],
            DirectoryList::VAR_DIR => [$path => $var],
            DirectoryList::VAR_EXPORT => [$path => "{$var}/export"],
            DirectoryList::MEDIA => [$path => "{$this->installDir}/pub/media"],
            DirectoryList::STATIC_VIEW => [$path => "{$this->installDir}/pub/static"],
            DirectoryList::TMP_MATERIALIZATION_DIR => [$path => "{$var}/view_preprocessed/pub/static"],
            DirectoryList::GENERATED_CODE => [$path => "{$generated}/code"],
            DirectoryList::CACHE => [$path => "{$var}/cache"],
            DirectoryList::LOG => [$path => "{$var}/log"],
            DirectoryList::SESSION => [$path => "{$var}/session"],
            DirectoryList::TMP => [$path => "{$var}/tmp"],
            DirectoryList::UPLOAD => [$path => "{$var}/upload"],
            DirectoryList::PUB => [$path => "{$this->installDir}/pub"],
            DirectoryList::VAR_IMPORT_EXPORT => [$path => "{$this->installDir}/var"],
        ];
        return $customDirs;
    }

    /**
     * Retrieve application initialization parameters.
     *
     * @return array
     */
    public function getInitParams()
    {
        return $this->_initParams;
    }

    /**
     * Reset and initialize again an already installed application
     *
     * @param array $overriddenParams
     * @return void
     */
    public function reinitialize(array $overriddenParams = [])
    {
        $this->_resetApp();
        $this->initialize($overriddenParams);
    }

    /**
     * Reset application global state
     */
    protected function _resetApp()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->clearCache();
        Form::setElementRenderer(null);
        Form::setFieldsetRenderer(null);
        Form::setFieldsetElementRenderer(null);
        $this->_appArea = null;
    }

    /**
     * Initialize application
     *
     * @param array $overriddenParams
     * @return void
     */
    public function initialize($overriddenParams = [])
    {
        $overriddenParams[State::PARAM_MODE] = $this->_appMode;
        $overriddenParams = $this->_customizeParams($overriddenParams);
        $directories = isset($overriddenParams[\Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS])
            ? $overriddenParams[\Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS]
            : [];
        $directoryList = new DirectoryList(BP, $directories);
        /** @var ObjectManager $objectManager */
        $objectManager = Helper\Bootstrap::getObjectManager();
        if (!$objectManager) {
            $objectManager = $this->_factory->create($overriddenParams);
            $objectManager->addSharedInstance(
                $directoryList,
                DirectoryList::class
            );
            $objectManager->addSharedInstance(
                $directoryList,
                \Magento\Framework\Filesystem\DirectoryList::class
            );
        } else {
            $objectManager = $this->_factory->restore($objectManager, $directoryList, $overriddenParams);
        }
        /** @var TestFramework\App\Filesystem $filesystem */
        $filesystem = $objectManager->get(TestFramework\App\Filesystem::class);
        $objectManager->removeSharedInstance(Filesystem::class);
        $objectManager->addSharedInstance($filesystem, Filesystem::class);
        Helper\Bootstrap::setObjectManager($objectManager);
        $this->initLogger();
        $sequenceBuilder = $objectManager->get(TestFramework\Db\Sequence\Builder::class);
        $objectManager->addSharedInstance($sequenceBuilder, Builder::class);

        $objectManagerConfiguration = [
            'preferences' => [
                State::class => TestFramework\App\State::class,
                Mail\TransportInterface::class => TestFramework\Mail\TransportInterfaceMock::class,
                Mail\Template\TransportBuilder::class => TestFramework\Mail\Template\TransportBuilderMock::class,
            ]
        ];
        if ($this->loadTestExtensionAttributes) {
            $objectManagerConfiguration = array_merge(
                $objectManagerConfiguration,
                [
                    \Magento\Framework\Api\ExtensionAttribute\Config\Reader::class => [
                        'arguments' => [
                            'fileResolver' => [
                                'instance' => TestFramework\Api\Config\Reader\FileResolver::class
                            ],
                        ],
                    ],
                ]
            );
        }
        $objectManager->configure($objectManagerConfiguration);
        /** Register event observer of Integration Framework */
        /** @var Data $eventConfigData */
        $eventConfigData = $objectManager->get(Data::class);
        $eventConfigData->merge(
            [
                'core_app_init_current_store_after' => [
                    'integration_tests' => [
                        'instance' => TestFramework\Event\Magento::class,
                        'name' => 'integration_tests'
                    ]
                ]
            ]
        );

        if ($this->canLoadArea) {
            $this->loadArea(TestFramework\Application::DEFAULT_APP_AREA);
        }

        TestFramework\Helper\Bootstrap::getObjectManager()->configure(
            $objectManager->get(DynamicConfigInterface::class)->getConfiguration()
        );
        Phrase::setRenderer(
            $objectManager->get(Placeholder::class)
        );

        if ($this->canInstallSequence) {
            /** @var Sequence $sequence */
            $sequence = $objectManager->get(Sequence::class);
            $sequence->generateSequences();
        }

        $objectManager->create(Config::class, ['configPath' => $this->globalConfigFile])
            ->rewriteAdditionalConfig();
    }

    /**
     * Sub-routine for merging custom parameters with the ones defined in object state
     *
     * @param array $params
     * @return array
     */
    public function _customizeParams($params)
    {
        return array_replace_recursive($this->_initParams, $params);
    }

    /**
     * Create logger instance and rewrite already exist one in ObjectManager.
     *
     * @return LoggerInterface
     */
    private function initLogger()
    {
        $objectManager = Helper\Bootstrap::getObjectManager();
        /** @var LoggerInterface $logger */
        $logger = $objectManager->create(
            TestFramework\ErrorLog\Logger::class,
            [
                'name' => 'integration-tests',
                'handlers' => [
                    'system' => $objectManager->create(
                        System::class,
                        [
                            'exceptionHandler' => $objectManager->create(
                                Exception::class,
                                ['filePath' => $this->installDir]
                            ),
                            'filePath' => $this->installDir
                        ]
                    ),
                    'debug' => $objectManager->create(
                        Debug::class,
                        ['filePath' => $this->installDir]
                    ),
                ]
            ]
        );
        $objectManager->removeSharedInstance(LoggerInterface::class, true);
        $objectManager->addSharedInstance($logger, LoggerInterface::class, true);
        return $logger;
    }

    /**
     * Load application area
     *
     * @param string $areaCode
     * @return void
     * @throws LocalizedException
     */
    public function loadArea($areaCode)
    {
        $this->_appArea = $areaCode;
        $scope = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            Scope::class
        );
        $scope->setCurrentScope($areaCode);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->configure(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                ConfigLoader::class
            )->load(
                $areaCode
            )
        );
        $app = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(AreaList::class);
        $areasForPartialLoading = [
            Area::AREA_GLOBAL,
            Area::AREA_WEBAPI_REST,
            Area::AREA_WEBAPI_SOAP,
            Area::AREA_CRONTAB,
            Area::AREA_GRAPHQL
        ];
        if (in_array($areaCode, $areasForPartialLoading, true)) {
            $app->getArea($areaCode)->load(Area::PART_CONFIG);
        } else {
            \Magento\TestFramework\Helper\Bootstrap::getInstance()->loadArea($areaCode);
        }
    }

    /**
     * Run application normally, but with encapsulated initialization options
     *
     * @return void
     */
    public function run()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var Http $app */
        $app = $objectManager->get(Http::class);
        $response = $app->launch();
        $response->sendResponse();
    }

    /**
     * Cleanup both the database and the file system
     *
     * @return void
     */
    public function cleanup()
    {
        $this->createInstallDir();
        /**
         * @see \Magento\Setup\Mvc\Bootstrap\InitParamListener::BOOTSTRAP_PARAM
         */
        $this->_shell->execute(
            PHP_BINARY . ' -f %s setup:uninstall -vvv -n --magento-init-params=%s',
            [BP . '/bin/magento', $this->getInitParamsQuery()]
        );
    }

    /**
     * Create install dir for integration framework
     *
     * @return void
     */
    public function createInstallDir()
    {
        $this->_ensureDirExists($this->installDir);
        $this->_ensureDirExists($this->_configDir);

        $this->copyAppConfigFiles();
    }

    /**
     * Create a directory with write permissions or don't touch existing one
     *
     * @param string $dir
     * @return void
     * @throws LocalizedException
     */
    protected function _ensureDirExists($dir)
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        if (!file_exists($dir)) {
            $old = umask(0);
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            mkdir($dir, 0777, true);
            umask($old);
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
        } elseif (!is_dir($dir)) {
            throw new LocalizedException(__("'%1' is not a directory.", $dir));
        }
    }

    /**
     * Copies configuration files from the main code base, so the installation could proceed in the tests directory
     *
     * @return void
     */
    private function copyAppConfigFiles()
    {
        $globalConfigFiles = Glob::glob(
            $this->_globalConfigDir . '/{di.xml,*/di.xml,db_schema.xml,vendor_path.php}',
            Glob::GLOB_BRACE
        );
        foreach ($globalConfigFiles as $file) {
            $targetFile = $this->_configDir . str_replace($this->_globalConfigDir, '', $file);
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $this->_ensureDirExists(dirname($targetFile));
            if ($file !== $targetFile) {
                // phpcs:ignore Magento2.Functions.DiscouragedFunction
                copy($file, $targetFile);
            }
        }
    }

    /**
     * Encodes init params into a query string
     *
     * @return string
     */
    private function getInitParamsQuery()
    {
        return urldecode(http_build_query($this->_initParams));
    }

    /**
     * Install an application
     *
     * @param bool $cleanup
     * @return void
     * @throws LocalizedException
     */
    public function install($cleanup)
    {
        $dirs = \Magento\Framework\App\Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS;
        $this->_ensureDirExists($this->installDir);
        $this->_ensureDirExists($this->_configDir);
        $this->_ensureDirExists($this->_initParams[$dirs][DirectoryList::PUB][DirectoryList::PATH]);
        $this->_ensureDirExists($this->_initParams[$dirs][DirectoryList::MEDIA][DirectoryList::PATH]);
        $this->_ensureDirExists($this->_initParams[$dirs][DirectoryList::STATIC_VIEW][DirectoryList::PATH]);
        $this->_ensureDirExists($this->_initParams[$dirs][DirectoryList::VAR_DIR][DirectoryList::PATH]);

        $this->copyAppConfigFiles();
        $this->copyGlobalConfigFile();

        $installParams = $this->getInstallCliParams();

        // performance optimization: restore DB from last good dump to make installation on top of it (much faster)
        // do not restore from the database if the cleanup option is set to ensure we have a clean DB to test on
        $db = $this->getDbInstance();
        if ($db->isDbDumpExists() && !$cleanup) {
            $db->restoreFromDbDump();
        }

        // run install script
        $this->_shell->execute(
            PHP_BINARY . ' -f %s setup:install -vvv ' . implode(' ', array_keys($installParams)),
            array_merge([BP . '/bin/magento'], array_values($installParams))
        );

        // run post-install setup commands
        $postInstallSetupCommands = $this->getPostInstallSetupCommands();

        foreach ($postInstallSetupCommands as $postInstallSetupCommand) {
            if (!isset($postInstallSetupCommand['command'])) {
                throw new DomainException('"command" must be present in post install setup command arrays');
            }

            $command = $postInstallSetupCommand['command'];
            $argumentsAndOptions = $postInstallSetupCommand['config'];

            $argumentsAndOptionsPlaceholders = [];

            foreach (array_keys($argumentsAndOptions) as $key) {
                $isArgument = is_numeric($key);

                if ($isArgument) {
                    $argumentsAndOptionsPlaceholders[] = '%s';
                } else {
                    $argumentsAndOptionsPlaceholders[] = "$key=%s";
                }
            }

            $argumentsAndOptionsPlaceholders[] = "--magento-init-params=%s";
            $argumentsAndOptions[] = $this->getInitParamsQuery();

            $this->_shell->execute(
                PHP_BINARY . ' -f %s %s -vvv ' . implode(' ', array_values($argumentsAndOptionsPlaceholders)),
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge
                array_merge(
                    [BP . '/bin/magento'],
                    [$command],
                    array_values($argumentsAndOptions)
                ),
            );
        }

        // enable only specified list of caches
        $initParamsQuery = $this->getInitParamsQuery();
        $this->_shell->execute(
            PHP_BINARY . ' -f %s cache:disable -vvv --bootstrap=%s',
            [BP . '/bin/magento', $initParamsQuery]
        );
        $this->_shell->execute(
            PHP_BINARY . ' -f %s cache:enable -vvv %s %s %s %s --bootstrap=%s',
            [
                BP . '/bin/magento',
                \Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER,
                Layout::TYPE_IDENTIFIER,
                Translate::TYPE_IDENTIFIER,
                Type::TYPE_IDENTIFIER,
                $initParamsQuery,
            ]
        );

        // right after a clean installation, store DB dump for future reuse in tests or running the test suite again
        if (!$db->isDbDumpExists() && $this->dumpDb) {
            $this->getDbInstance()->storeDbDump();
        }
    }

    /**
     * Copies global configuration file from the tests folder (see TESTS_GLOBAL_CONFIG_FILE)
     *
     * @return void
     */
    private function copyGlobalConfigFile()
    {
        $targetFile = $this->_configDir . '/config.local.php';
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        copy($this->globalConfigFile, $targetFile);
    }

    /**
     * Gets a list of CLI params for installation
     *
     * @return array
     */
    private function getInstallCliParams()
    {
        $params = $this->getInstallConfig();
        /**
         * Literal value is used instead of constant, because autoloader is not integrated with Magento Setup app
         * @see \Magento\Setup\Mvc\Bootstrap\InitParamListener::BOOTSTRAP_PARAM
         */
        $params['magento-init-params'] = $this->getInitParamsQuery();
        $result = [];
        foreach ($params as $key => $value) {
            if (isset($value)) {
                $result["--{$key}=%s"] = $value;
            }
        }
        return $result;
    }

    /**
     * Gets installation parameters.
     *
     * @return array
     */
    protected function getInstallConfig()
    {
        if (null === $this->installConfig) {
            // phpcs:ignore Magento2.Security.IncludeFile
            $this->installConfig = include $this->installConfigFile;
        }
        return $this->installConfig;
    }

    /**
     * Retrieve the database adapter instance.
     *
     * @return TestFramework\Db\AbstractDb
     */
    public function getDbInstance()
    {
        if (null === $this->_db) {
            if ($this->isInstalled()) {
                $configPool = new ConfigFilePool();
                $driverPool = new DriverPool();
                $reader = new Reader($this->dirList, $driverPool, $configPool);
                $deploymentConfig = new DeploymentConfig($reader, []);
                $host = $deploymentConfig->get(
                    ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT .
                    '/' . ConfigOptionsListConstants::KEY_HOST
                );
                $user = $deploymentConfig->get(
                    ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT .
                    '/' . ConfigOptionsListConstants::KEY_USER
                );
                $password = $deploymentConfig->get(
                    ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT .
                    '/' . ConfigOptionsListConstants::KEY_PASSWORD
                );
                $dbName = $deploymentConfig->get(
                    ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT .
                    '/' . ConfigOptionsListConstants::KEY_NAME
                );
            } else {
                $installConfig = $this->getInstallConfig();
                $host = $installConfig['db-host'];
                $user = $installConfig['db-user'];
                $password = $installConfig['db-password'];
                $dbName = $installConfig['db-name'];
            }
            $this->_db = new Db\Mysql(
                $host,
                $user,
                $password,
                $dbName,
                $this->getTempDir(),
                $this->_shell
            );
        }
        return $this->_db;
    }

    /**
     * Weather the application is installed or not.
     *
     * @return bool
     */
    public function isInstalled()
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        return is_file($this->getLocalConfig());
    }

    /**
     * Gets deployment configuration path.
     *
     * @return string
     */
    private function getLocalConfig()
    {
        return $this->_configDir . '/config.php';
    }

    /**
     * Get path to temporary directory.
     *
     * @return string
     */
    public function getTempDir()
    {
        return $this->installDir;
    }

    /**
     * Gets post-installation commands.
     *
     * @return array
     */
    protected function getPostInstallSetupCommands()
    {
        if (null === $this->postInstallSetupCommandsFile) {
            $this->postInstallSetupCommands = [];
        } elseif (null === $this->postInstallSetupCommands) {
            // phpcs:ignore Magento2.Security.IncludeFile
            $this->postInstallSetupCommands = include $this->postInstallSetupCommandsFile;
        }
        return $this->postInstallSetupCommands;
    }

    /**
     * Ge current application area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_appArea;
    }
}
