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
    CategorySetup::class,
    ['resourceName' => 'catalog_setup']
);
/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->loadByCode(Product::ENTITY, 'select_attribute');

/** @var $selectOptions Collection */
$selectOptions = Bootstrap::getObjectManager()->create(
    Collection::class
);
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$selectOptions->setAttributeFilter($attribute->getId());
/* Delete simple products per each select(dropdown) option */
foreach ($selectOptions as $option) {
    /** @var $product Product */
    $product = Bootstrap::getObjectManager()->create(
        Product::class
    );
    $product = $product->loadByAttribute('sku', 'simple_product_' . $option->getId());
    if ($product->getId()) {
        $product->delete();
    }
}
if ($attribute->getId()) {
    $attribute->delete();
}

$attribute->loadByCode($installer->getEntityTypeId('catalog_product'), 'multiselect_attribute');
if ($attribute->getId()) {
    $attribute->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
