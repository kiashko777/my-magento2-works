<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$skuList = ['simple_100000001', 'simple_100000002', 'grouped'];
foreach ($skuList as $sku) {
    try {
        $product = $productRepository->get($sku, false, null, true);

        $stockStatus = $objectManager->create(Status::class);
        $stockStatus->load($product->getData('entity_id'), 'product_id');
        $stockStatus->delete();

        $productRepository->delete($product);
    } catch (NoSuchEntityException $e) {
        //Products already removed
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
