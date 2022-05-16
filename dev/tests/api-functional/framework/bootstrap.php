<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Autoload\AutoloaderRegistry;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\DirSearch;
use Magento\Framework\Logger\Handler\Debug;
use Magento\Framework\Logger\Handler\System;
use Magento\Framework\Profiler\Driver\Standard;
use Magento\Framework\Shell;
use Magento\Framework\Shell\CommandRenderer;
use Magento\Framework\View\Design\Theme\ThemePackageList;
use Magento\TestFramework\Bootstrap\Environment;
use Magento\TestFramework\Bootstrap\MemoryFactory;
use Magento\TestFramework\Bootstrap\Settings;
use Magento\TestFramework\Bootstrap\WebapiDocBlock;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\WebApiApplication;
use Magento\TestFramework\Workaround\Override\Config;
use Monolog\Logger;

require_once __DIR__ . '/../../../../app/bootstrap.php';
require_once __DIR__ . '/autoload.php';

$testsBaseDir = dirname(__DIR__);
$integrationTestsDir = realpath("{$testsBaseDir}/../integration");
$fixtureBaseDir = $integrationTestsDir . '/testsuite';

if (!defined('TESTS_TEMP_DIR')) {
    define('TESTS_TEMP_DIR', $testsBaseDir . '/tmp');
}

if (!defined('INTEGRATION_TESTS_DIR')) {
    define('INTEGRATION_TESTS_DIR', $integrationTestsDir);
}

try {
    setCustomErrorHandler();

    /* Bootstrap the application */
    $settings = new Settings($testsBaseDir, get_defined_constants());

    if ($settings->get('TESTS_EXTRA_VERBOSE_LOG')) {
        $filesystem = new \Magento\Framework\Filesystem\Driver\File();
        $exceptionHandler = new \Magento\Framework\Logger\Handler\Exception($filesystem);
        $loggerHandlers = [
            'system' => new System($filesystem, $exceptionHandler),
            'debug' => new Debug($filesystem)
        ];
        $shell = new Shell(
            new CommandRenderer(),
            new Logger('main', $loggerHandlers)
        );
    } else {
        $shell = new Shell(new CommandRenderer());
    }

    $testFrameworkDir = __DIR__;
    require_once INTEGRATION_TESTS_DIR . '/framework/deployTestModules.php';

    $installConfigFile = $settings->getAsConfigFile('TESTS_INSTALL_CONFIG_FILE');
    if (!file_exists($installConfigFile)) {
        $installConfigFile = $installConfigFile . '.dist';
    }
    $globalConfigFile = $settings->getAsConfigFile('TESTS_GLOBAL_CONFIG_FILE');
    if (!file_exists($installConfigFile)) {
        $installConfigFile = $installConfigFile . '.dist';
    }
    $dirList = new DirectoryList(BP);
    $application = new WebApiApplication(
        $shell,
        $dirList->getPath(DirectoryList::VAR_DIR),
        $installConfigFile,
        $globalConfigFile,
        BP . '/app/etc/',
        $settings->get('TESTS_MAGENTO_MODE'),
        AutoloaderRegistry::getAutoloader()
    );

    if (defined('TESTS_MAGENTO_INSTALLATION') && TESTS_MAGENTO_INSTALLATION === 'enabled') {
        $cleanup = (defined('TESTS_CLEANUP') && TESTS_CLEANUP === 'enabled');
        $application->install($cleanup);
    }

    $bootstrap = new \Magento\TestFramework\Bootstrap(
        $settings,
        new Environment(),
        new WebapiDocBlock("{$integrationTestsDir}/testsuite"),
        new \Magento\TestFramework\Bootstrap\Profiler(new Standard()),
        $shell,
        $application,
        new MemoryFactory($shell)
    );
    $bootstrap->runBootstrap();
    $application->initialize();

    Bootstrap::setInstance(new Bootstrap($bootstrap));
    $dirSearch = Bootstrap::getObjectManager()
        ->create(DirSearch::class);
    $themePackageList = Bootstrap::getObjectManager()
        ->create(ThemePackageList::class);
    Files::setInstance(
        new Files(
            new ComponentRegistrar(),
            $dirSearch,
            $themePackageList
        )
    );
    $overrideConfig = Bootstrap::getObjectManager()->create(
        Magento\TestFramework\WebapiWorkaround\Override\Config::class
    );
    $overrideConfig->init();
    Magento\TestFramework\Workaround\Override\Fixture\Resolver::setInstance(
        new  \Magento\TestFramework\WebapiWorkaround\Override\Fixture\Resolver($overrideConfig)
    );
    Config::setInstance($overrideConfig);
    unset($bootstrap, $application, $settings, $shell, $overrideConfig);
} catch (Exception $e) {
    // phpcs:ignore Magento2.Security.LanguageConstruct.DirectOutput
    echo $e . PHP_EOL;
    // phpcs:ignore Magento2.Security.LanguageConstruct.ExitUsage
    exit(1);
}

/**
 * Set custom error handler
 */
function setCustomErrorHandler()
{
    set_error_handler(
        function ($errNo, $errStr, $errFile, $errLine) {
            if (error_reporting()) {
                $errorNames = [
                    E_ERROR => 'Error',
                    E_WARNING => 'Warning',
                    E_PARSE => 'Parse',
                    E_NOTICE => 'Notice',
                    E_CORE_ERROR => 'Core Error',
                    E_CORE_WARNING => 'Core Warning',
                    E_COMPILE_ERROR => 'Compile Error',
                    E_COMPILE_WARNING => 'Compile Warning',
                    E_USER_ERROR => 'User Error',
                    E_USER_WARNING => 'User Warning',
                    E_USER_NOTICE => 'User Notice',
                    E_STRICT => 'Strict',
                    E_RECOVERABLE_ERROR => 'Recoverable Error',
                    E_DEPRECATED => 'Deprecated',
                    E_USER_DEPRECATED => 'User Deprecated',
                ];

                $errName = isset($errorNames[$errNo]) ? $errorNames[$errNo] : "";

                throw new \PHPUnit\Framework\Exception(
                    sprintf("%s: %s in %s:%s.", $errName, $errStr, $errFile, $errLine),
                    $errNo
                );
            }
        }
    );
}
