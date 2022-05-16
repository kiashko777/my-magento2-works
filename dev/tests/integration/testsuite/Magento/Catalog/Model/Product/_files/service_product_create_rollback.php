<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Product $productModel */
$productModel = Bootstrap::getObjectManager()->get(Product::class);
$productModel->load($productModel->getIdBySku('psku-test-1'));
if ($productModel->getId()) {
    $productModel->delete();
}

/** @var Product $productModel */
$productModel = Bootstrap::getObjectManager()->get(Product::class);
$productModel->load($productModel->getIdBySku('psku-test-2'));
if ($productModel->getId()) {
    $productModel->delete();
}
