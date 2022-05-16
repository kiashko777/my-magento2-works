<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);

$product = $product->loadByAttribute('sku', 'simple-with-ftp');
if ($product && $product->getId()) {
    $product->delete();
}

/** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
$attribute = Bootstrap::getObjectManager()
    ->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
$attribute->load('fpt_for_all', 'attribute_code');
if ($attribute->getId()) {
    $attribute->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
