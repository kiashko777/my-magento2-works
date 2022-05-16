<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* Create attribute */
/** @var $installer CategorySetup */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$installer = Bootstrap::getObjectManager()->create(
    CategorySetup::class,
    ['resourceName' => 'catalog_setup']
);
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product = $product->loadByAttribute('sku', 'simple_product_with_date_attribute');
if ($product->getId()) {
    $product->delete();
}

/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->loadByCode($installer->getEntityTypeId('catalog_product'), 'date_attribute');
if ($attribute->getId()) {
    $attribute->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
