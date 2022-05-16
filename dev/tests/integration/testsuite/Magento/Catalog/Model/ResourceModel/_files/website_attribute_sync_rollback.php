<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext\Processor;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Roll back fixtures
 *  - Remove Products
 *  - Remove Website/StoreGroup/[Store1, Store2]
 *  - ReIndex Full text indexers
 */

$productId = 333;
$objectManager = Bootstrap::getObjectManager();
$storeRepository = $objectManager->get(StoreRepositoryInterface::class);
$resourceConnection = $objectManager->get(ResourceConnection::class);
/**
 * @var AdapterInterface $connection
 */
$connection = $resourceConnection->getConnection();
$registry = $objectManager->get(Registry::class);
$productRepository = $objectManager->get(ProductRepositoryInterface::class);


/**
 * Marks area as secure so Products repository would allow product removal
 */
$isSecuredAreaSystemState = $registry->registry('isSecuredArea');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/**
 * @var Store $store
 */
$store = $storeRepository->get('customstoreview1');
$storeGroupId = $store->getStoreGroupId();
$websiteId = $store->getWebsiteId();

try {
    $product = $productRepository->getById($productId);
    if ($product->getId()) {
        $productRepository->delete($product);
    }
} catch (NoSuchEntityException $e) {
    //Products already removed
}

/**
 * Remove stores by code
 */
$storeCodes = [
    'customstoreview1',
    'customstoreview2',
];

$connection->delete(
    $resourceConnection->getTableName('store'),
    [
        'code IN (?)' => $storeCodes,
    ]
);

/**
 * removeStoreGroupById
 */
$connection->delete(
    $resourceConnection->getTableName('store_group'),
    [
        'group_id = ?' => $storeGroupId,
    ]
);

/**
 * remove website by id
 */
/** @var Website $website */
$website = Bootstrap::getObjectManager()->create(Website::class);
$website->load((int)$websiteId);
$website->delete();

/**
 * reIndex all
 */
ObjectManager::getInstance()
    ->create(Processor::class)
    ->reindexAll();

/**
 * Revert mark area secured
 */
$registry->unregister('isSecuredArea');
$registry->register('isSecuredArea', $isSecuredAreaSystemState);
