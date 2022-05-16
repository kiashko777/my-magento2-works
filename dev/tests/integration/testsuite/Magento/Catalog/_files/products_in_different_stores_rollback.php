<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/core_fixturestore_rollback.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

//Remove category
/** @var $category Category */
$category = $objectManager->create(Category::class);
$category->load(96377);
if ($category->getId()) {
    $category->delete();
}

$productSkuList = ['simple_1', 'simple_2', 'simple_3'];
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

/** @var Magento\Store\Model\Website $website */
$website = $objectManager->get(Magento\Store\Model\Website::class);
$website->load('second_website', 'code');
if ($website->getId()) {
    $website->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
