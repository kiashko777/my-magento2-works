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
use Magento\Framework\Profiler\Driver\Standard;
use Magento\Framework\Shell;
use Magento\Framework\Shell\CommandRenderer;
use Magento\Framework\View\Design\Theme\ThemePackageList;
use Magento\TestFramework\Bootstrap\Environment;
use Magento\TestFramework\Bootstrap\MemoryFactory;
use Magento\TestFramework\Bootstrap\Settings;
use Magento\TestFramework\Bootstrap\SetupDocBlock;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\SetupApplication;

require_once __DIR__ . '/../../../../app/bootstrap.php';
require_once __DIR__ . '/autoload.php';
//to handle different types of errors on CI
require __DIR__ . '/../../error_handler.php';

$testsBaseDir = dirname(__DIR__);
$integrationTestsDir = realpath("{$testsBaseDir}/../integration");
$fixtureBaseDir = $integrationTestsDir . '/testsuite';

if (!defined('TESTS_BASE_DIR')) {
    define('TESTS_BASE_DIR', $testsBaseDir);
}

if (!defined('TESTS_TEMP_DIR')) {
    define('TESTS_TEMP_DIR', $testsBaseDir . '/tmp');
}

if (!defined('TESTS_MODULES_PATH')) {
    define('TESTS_MODULES_PATH', $testsBaseDir . '/_files');
}

if (!defined('MAGENTO_MODULES_PATH')) {
    define('MAGENTO_MODULES_PATH', __DIR__ . '/../../../../app/code/Magento/');
}

if (!defined('INTEGRATION_TESTS_BASE_DIR')) {
    define('INTEGRATION_TESTS_BASE_DIR', $integrationTestsDir);
}
$settings = new Settings($testsBaseDir, get_defined_constants());

try {
    setCustomErrorHandler();
    $installConfigFile = $settings->getAsConfigFile('TESTS_INSTALL_CONFIG_FILE');
    if (!file_exists($installConfigFile)) {
        $installConfigFile .= '.dist';
    }
    if (!defined('TESTS_INSTALLATION_DB_CONFIG_FILE')) {
        define('TESTS_INSTALLATION_DB_CONFIG_FILE', $installConfigFile);
    }
    /* Bootstrap the application */
    $shell = new Shell(new CommandRenderer());
    $testFrameworkDir = __DIR__;

    $globalConfigFile = $settings->getAsConfigFile('TESTS_GLOBAL_CONFIG_FILE');
    if (!file_exists($globalConfigFile)) {
        $globalConfigFile .= '.dist';
    }

    $dirList = new DirectoryList(BP);
    $installDir = TESTS_TEMP_DIR;
    $application = new SetupApplication(
        $shell,
        $installDir,
        $installConfigFile,
        $globalConfigFile,
        $settings->get('TESTS_GLOBAL_CONFIG_DIR'),
        $settings->get('TESTS_MAGENTO_MODE'),
        AutoloaderRegistry::getAutoloader(),
        false
    );

    $bootstrap = new \Magento\TestFramework\Bootstrap(
        $settings,
        new Environment(),
        new SetupDocBlock("{$testsBaseDir}/_files/"),
        new \Magento\TestFramework\Bootstrap\Profiler(new Standard()),
        $shell,
        $application,
        new MemoryFactory($shell)
    );
    //remove test modules files
    include_once __DIR__ . '/../../setup-integration/framework/removeTestModules.php';
    $bootstrap->runBootstrap();
    $application->createInstallDir();
    //We do not want to install anything
    $application->initialize([]);
    $application->cleanup();

    Bootstrap::setInstance(new Bootstrap($bootstrap));

    $dirSearch = Bootstrap::getObjectManager()
        ->create(DirSearch::class);
    $themePackageList = Bootstrap::getObjectManager()
        ->create(ThemePackageList::class);
    Files::setInstance(
        new Magento\Framework\App\Utility\Files(
            new ComponentRegistrar(),
            $dirSearch,
            $themePackageList
        )
    );

    /* Unset declared global variables to release the PHPUnit from maintaining their values between tests */
    unset($testsBaseDir, $settings, $shell, $application, $bootstrap);
} catch (Exception $e) {
    // phpcs:disable Magento2.Security.LanguageConstruct
    echo $e . PHP_EOL;
    exit(1);
    // phpcs:enable Magento2.Security.LanguageConstruct
}
