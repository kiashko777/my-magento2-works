<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/multiselect_attribute_rollback.php');

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);
try {
    $product = $productRepository->get('simple_ms_out_of_stock', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $e) {
}

Bootstrap::getObjectManager()->get(IndexerRegistry::class)
    ->get(Magento\CatalogInventory\Model\Indexer\Stock\Processor::INDEXER_ID)
    ->reindexAll();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
