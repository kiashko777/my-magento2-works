<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Helper\Data;
use Magento\Catalog\Observer\SwitchPriceAttributeScopeOnConfigChange;
use Magento\Config\App\Config\Type\System;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Directory\Model\Currency;
use Magento\Framework\Event\Observer;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_website_with_two_stores.php');

$objectManager = Bootstrap::getObjectManager();
/** @var WebsiteRepositoryInterface $websiteRepository */
$websiteRepository = $objectManager->get(WebsiteRepositoryInterface::class);
$websiteId = $websiteRepository->get('test')->getId();
/** @var Config $configResource */
$configResource = $objectManager->get(Config::class);
$configResource->saveConfig(
    Currency::XML_PATH_CURRENCY_BASE,
    'EUR',
    ScopeInterface::SCOPE_WEBSITES,
    $websiteId
);
$configResource->saveConfig(
    Data::XML_PATH_PRICE_SCOPE,
    Store::PRICE_SCOPE_WEBSITE,
    'default',
    0
);

/**
 * Configuration cache clean is required to reload currency setting
 */
/** @var Magento\Config\App\Config\Type\System $config */
$config = $objectManager->get(System::class);
$config->clean();

$observer = $objectManager->get(Observer::class);
$objectManager->get(SwitchPriceAttributeScopeOnConfigChange::class)
    ->execute($observer);
