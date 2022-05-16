<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

$website = Bootstrap::getObjectManager()->create(Website::class);
/** @var $website Website */
if (!$website->load('test', 'code')->getId()) {
    $website->setData(['code' => 'test', 'name' => 'Test Website', 'default_group_id' => '1', 'is_default' => '0']);
    $website->save();
}
$websiteId = $website->getId();
$store = Bootstrap::getObjectManager()->create(Store::class);
if (!$store->load('fixture_second_store', 'code')->getId()) {
    $groupId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getWebsite()->getDefaultGroupId();
    $store->setCode(
        'fixture_second_store'
    )->setWebsiteId(
        $websiteId
    )->setGroupId(
        $groupId
    )->setName(
        'Fixture Second Store'
    )->setSortOrder(
        10
    )->setIsActive(
        1
    );
    $store->save();
}

$store = Bootstrap::getObjectManager()->create(Store::class);
if (!$store->load('fixture_third_store', 'code')->getId()) {
    $groupId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getWebsite()->getDefaultGroupId();
    $store->setCode(
        'fixture_third_store'
    )->setWebsiteId(
        $websiteId
    )->setGroupId(
        $groupId
    )->setName(
        'Fixture Third Store'
    )->setSortOrder(
        11
    )->setIsActive(
        1
    );
    $store->save();
}

/* Refresh CatalogSearch index */
/** @var IndexerRegistry $indexerRegistry */
$indexerRegistry = Bootstrap::getObjectManager()
    ->create(IndexerRegistry::class);
$indexerRegistry->get(Fulltext::INDEXER_ID)->reindexAll();
