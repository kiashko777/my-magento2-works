<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Autoload\AutoloaderRegistry;

require_once __DIR__ . '/../../../../app/autoload.php';

$testsBaseDir = dirname(__DIR__);
$integrationTestsDir = realpath("{$testsBaseDir}/../integration/");

$autoloadWrapper = AutoloaderRegistry::getAutoloader();
$autoloadWrapper->addPsr4('Magento\\TestFramework\\', "{$testsBaseDir}/framework/Magento/TestFramework/");
$autoloadWrapper->addPsr4('Magento\\TestFramework\\', "{$integrationTestsDir}/framework/Magento/TestFramework/");
$autoloadWrapper->addPsr4('Magento\\', "{$testsBaseDir}/testsuite/Magento/");

// registration of classes under '../_files'
$autoloadWrapper->addPsr4('Magento\\', "{$testsBaseDir}/_files/");
