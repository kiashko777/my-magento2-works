<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ObjectManagerInterface $objectManager */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$productSkuList = ['simple_product_1', 'simple_product_2'];
foreach ($productSkuList as $sku) {
    try {
        $productRepository = Bootstrap::getObjectManager()
            ->get(ProductRepositoryInterface::class);
        $product = $productRepository->get($sku, true);
        if ($product->getId()) {
            $productRepository->delete($product);
        }
    } catch (NoSuchEntityException $e) {
        //Products already removed
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
