<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$registry = Bootstrap::getObjectManager()->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $productCollection Collection */
$productCollection = Bootstrap::getObjectManager()
    ->create(Collection::class);

$productCollection->load()->delete();

$productSkuList = ['simple', '12345'];
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

Resolver::getInstance()->requireDataFixture('Magento/CatalogUrlRewrite/_files/categories_rollback.php');
