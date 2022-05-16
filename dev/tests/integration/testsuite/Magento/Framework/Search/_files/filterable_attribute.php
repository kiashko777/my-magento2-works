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
    CategorySetup::class,
    ['resourceName' => 'catalog_setup']
);
/** @var $selectAttribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$selectAttribute = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$selectAttribute->setData(
    [
        'attribute_code' => 'select_attribute',
        'entity_type_id' => $installer->getEntityTypeId('catalog_product'),
        'is_global' => 1,
        'frontend_input' => 'select',
        'is_filterable' => 1,
        'is_user_defined' => 1,
        'option' => [
            'value' => ['option_0' => ['Option 1'], 'option_1' => ['Option 2']],
            'order' => ['option_0' => 1, 'option_1' => 2],
        ],
        'backend_type' => 'int',
    ]
);
$selectAttribute->save();
/* Assign attribute to attribute set */
$installer->addAttributeToGroup('catalog_product', 'Default', 'General', $selectAttribute->getId());

/** @var $selectOptions Collection */
$selectOptions = Bootstrap::getObjectManager()->create(
    Collection::class
);
$selectOptions->setAttributeFilter($selectAttribute->getId());

$multiselectAttribute = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$multiselectAttribute->setData(
    [
        'attribute_code' => 'multiselect_attribute',
        'entity_type_id' => $installer->getEntityTypeId('catalog_product'),
        'is_global' => 1,
        'frontend_input' => 'multiselect',
        'is_filterable' => 1,
        'is_user_defined' => 1,
        'option' => [
            'value' => ['option_0' => ['Option 1'], 'option_1' => ['Option 2']],
            'order' => ['option_0' => 1, 'option_1' => 2],
        ],
        'backend_type' => 'varchar',
    ]
);
$multiselectAttribute->save();
/* Assign attribute to attribute set */
$installer->addAttributeToGroup('catalog_product', 'Default', 'General', $multiselectAttribute->getId());

/** @var $multiselectOptions Collection */
$multiselectOptions = Bootstrap::getObjectManager()->create(
    Collection::class
);
$multiselectOptions->setAttributeFilter($multiselectAttribute->getId());

/* Create simple products per each select(dropdown) option */
foreach ($selectOptions as $option) {
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
        99
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
        [
            $selectAttribute->getAttributeCode() => $option->getId(),
            $multiselectAttribute->getAttributeCode() => $multiselectOptions->getLastItem()->getId()
        ],
        $product->getStoreId()
    );
}
