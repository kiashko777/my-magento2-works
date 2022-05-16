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
use Magento\Store\Model\Group;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
//Creating second website with a store.
/** @var $website Website */
$website = $objectManager->create(Website::class);
$website->load('test', 'code');

if (!$website->getId()) {
    $website->setData([
        'code' => 'test',
        'name' => 'Test Website',
        'is_default' => '0',
    ]);
    $website->save();
}

/**
 * @var Group $storeGroup
 */
$storeGroup = $objectManager->create(Group::class);
$storeGroup->setCode('some_group')
    ->setName('custom store group')
    ->setWebsite($website);
$storeGroup->save($storeGroup);

$website->setDefaultGroupId($storeGroup->getId());
$website->save($website);

$websiteId = $website->getId();
$store = $objectManager->create(Store::class);
$store->load('fixture_second_store', 'code');

if (!$store->getId()) {
    $groupId = $website->getDefaultGroupId();
    $store->setData([
        'code' => 'fixture_second_store',
        'website_id' => $websiteId,
        'group_id' => $groupId,
        'name' => 'Fixture Second Store',
        'sort_order' => 10,
        'is_active' => 1,
    ]);
    $store->save();
}

//Setting up allowed countries
$configResource = $objectManager->get(Config::class);
//Allowed countries for default website.
$configResource->saveConfig(
    'general/country/allow',
    'FR',
    ScopeInterface::SCOPE_WEBSITES,
    1
);
//Allowed countries for second website
$configResource->saveConfig(
    'general/country/allow',
    'ES,US,UK,DE',
    ScopeInterface::SCOPE_WEBSITES,
    $websiteId
);

/* Refresh CatalogSearch index */
/** @var IndexerRegistry $indexerRegistry */
$indexerRegistry = $objectManager->create(IndexerRegistry::class);
$indexerRegistry->get(FulltextIndex::INDEXER_ID)->reindexAll();
//Clear config cache.
$objectManager->get(ReinitableConfigInterface::class)->reinit();
