<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* Create attribute */
/** @var $installer CategorySetup */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$installer = Bootstrap::getObjectManager()->create(
    CategorySetup::class
);
/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->loadByCode(Product::ENTITY, 'attribute_with_option');

/* Delete simple products per each option */
/** @var $options Collection */
$options = Bootstrap::getObjectManager()->create(
    Collection::class
);
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
$options->setAttributeFilter($attribute->getId());

foreach ($options as $option) {
    /** @var $product Product */
    $product = Bootstrap::getObjectManager()->create(
        Product::class
    );
    $product = $product->loadByAttribute('sku', 'simple_product_' . $option->getId());
    if ($product instanceof Product && $product->getId()) {
        $product->delete();
    }
}

if ($attribute->getId()) {
    $attribute->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
