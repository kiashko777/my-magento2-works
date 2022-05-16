<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\CatalogSearch\Model\Indexer\Fulltext as FulltextIndex;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

//Deleting second website's store.
$store = $objectManager->create(Store::class);
if ($store->load('fixture_second_store', 'code')->getId()) {
    $store->delete();
}

//Deleting the second website.

$configResource = $objectManager->get(Config::class);
//Restoring allowed countries.
$configResource->deleteConfig(
    'general/country/allow',
    ScopeInterface::SCOPE_WEBSITES,
    1
);
/** @var Website $website */
$website = $objectManager->create(Website::class);
$website->load('test');
if ($website->getId()) {
    $configResource->deleteConfig(
        'general/country/allow',
        ScopeInterface::SCOPE_WEBSITES,
        $website->getId()
    );
    $website->delete();
}
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

/* Refresh stores memory cache */
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
$storeManager->reinitStores();
/* Refresh CatalogSearch index */
/** @var IndexerRegistry $indexerRegistry */
$indexerRegistry = $objectManager->create(IndexerRegistry::class);
$indexerRegistry->get(FulltextIndex::INDEXER_ID)->reindexAll();
//Clear config cache.
$objectManager->get(ReinitableConfigInterface::class)->reinit();
