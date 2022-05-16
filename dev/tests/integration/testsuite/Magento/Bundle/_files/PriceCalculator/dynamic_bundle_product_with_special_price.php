<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/PriceCalculator/dynamic_bundle_product.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

/** @var $product Product */
$productRepository
    ->get('bundle_product')
    ->setSpecialPrice(50)
    ->save();

$productRepository
    ->get('simple2')
    ->setSpecialPrice(2.5)
    ->save();

$productRepository
    ->get('simple5')
    ->setSpecialPrice(9.9)
    ->save();
