<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Config\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$configData = [
    'sendfriend/email/max_per_hour' => 1,
    'sendfriend/email/check_by' => 1,

];
$objectManager = Bootstrap::getObjectManager();
$defConfig = $objectManager->create(Config::class);
$defConfig->setScope(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
foreach ($configData as $key => $value) {
    $defConfig->setDataByPath($key, $value);
    $defConfig->save();
}
