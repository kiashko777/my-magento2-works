<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* Create attribute */
/** @var $installer CategorySetup */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\TestFramework\Helper\Bootstrap;

$installer = Bootstrap::getObjectManager()->create(
    CategorySetup::class
);
/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->setData(
    [
        'attribute_code' => 'attribute_with_option',
        'entity_type_id' => $installer->getEntityTypeId('catalog_product'),
        'is_global' => 1,
        'frontend_input' => 'select',
        'is_filterable' => 1,
        'is_user_defined' => 1,
        'option' => ['value' => ['option_0' => [0 => 'Option Label']]],
        'backend_type' => 'int',
    ]
);
$attribute->save();

/* Assign attribute to attribute set */
$installer->addAttributeToGroup('catalog_product', 'Default', 'General', $attribute->getId());

/* Create simple products per each option */
/** @var $options Collection */
$options = Bootstrap::getObjectManager()->create(
    Collection::class
);
$options->setAttributeFilter($attribute->getId());

foreach ($options as $option) {
    /** @var $product Product */
    $product = Bootstrap::getObjectManager()->create(
        Product::class
    );
    $product->setTypeId(
        \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
    )->setAttributeSetId(
        $installer->getAttributeSetId('catalog_product', 'Default')
    )->setWebsiteIds(
        [1]
    )->setName(
        'Simple Products ' . $option->getId()
    )->setSku(
        'simple_product_' . $option->getId()
    )->setPrice(
        10
    )->setCategoryIds(
        [2]
    )->setVisibility(
        Visibility::VISIBILITY_BOTH
    )->setStatus(
        Status::STATUS_ENABLED
    )->setStockData(
        ['use_config_manage_stock' => 1, 'qty' => 5, 'is_in_stock' => 1]
    )->save();

    Bootstrap::getObjectManager()->get(
        Action::class
    )->updateAttributes(
        [$product->getId()],
        [$attribute->getAttributeCode() => $option->getId()],
        $product->getStoreId()
    );
}
