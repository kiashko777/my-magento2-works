<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Config\App\Config\Type\System;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Directory\Model\ResourceModel\Currency;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_store.php');

$objectManager = Bootstrap::getObjectManager();
$store = $objectManager->create(Store::class);
if ($storeId = $store->load('fixture_second_store', 'code')->getId()) {
    /** @var Config $configResource */
    $configResource = $objectManager->get(Config::class);
    $configResource->saveConfig(
        \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_DEFAULT,
        'EUR',
        ScopeInterface::SCOPE_STORES,
        $storeId
    );
    $configResource->saveConfig(
        \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_ALLOW,
        'EUR',
        ScopeInterface::SCOPE_STORES,
        $storeId
    );
    /**
     * Configuration cache clean is required to reload currency setting
     */
    /** @var Magento\Config\App\Config\Type\System $config */
    $config = $objectManager->get(System::class);
    $config->clean();
}


/** @var Currency $rate */
$rate = $objectManager->create(Currency::class);
$rate->saveRates(
    [
        'USD' => ['EUR' => 2],
        'EUR' => ['USD' => 0.5]
    ]
);
