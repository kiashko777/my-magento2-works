<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Helper\Data;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();
/** @var Config $configResource */
$configResource = $objectManager->get(Config::class);
$configResource->deleteConfig(
    Data::XML_PATH_PRICE_SCOPE,
    'default',
    0
);
$website = $objectManager->create(Website::class);
/** @var $website Website */
$websiteId = $website->load('test', 'code')->getId();
if ($websiteId) {
    $configResource->deleteConfig(
        Currency::XML_PATH_CURRENCY_BASE,
        ScopeInterface::SCOPE_WEBSITES,
        $websiteId
    );
}

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_website_with_two_stores_rollback.php');
