<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$productRepository = $objectManager->get(ProductRepositoryInterface::class);
$productSkuList = ['simple', '12345'];
foreach ($productSkuList as $sku) {
    try {
        $product = $productRepository->get($sku, true, null, true);
        $productRepository->delete($product);
    } catch (NoSuchEntityException $e) {
        //Products already removed
        continue;
    }
}

$collection = $objectManager->create(Collection::class);
$collection->addAttributeToFilter('level', 2)
    ->load()
    ->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
