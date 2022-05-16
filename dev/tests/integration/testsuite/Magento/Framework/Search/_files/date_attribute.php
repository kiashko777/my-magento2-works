<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* Create attribute */

/** @var ObjectManagerInterface $objectManager */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var $installer CategorySetup */
$installer = $objectManager->create(
    CategorySetup::class,
    ['resourceName' => 'catalog_setup']
);

/** @var $selectAttribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$dateAttribute = $objectManager->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$dateAttribute->setData(
    [
        'attribute_code' => 'date_attribute',
        'entity_type_id' => $installer->getEntityTypeId('catalog_product'),
        'is_global' => 1,
        'is_filterable' => 1,
        'is_user_defined' => 1,
        'backend_type' => 'datetime',
        'frontend_input' => 'date',
        'frontend_label' => 'Test Date',
    ]
);
$dateAttribute->save();
/* Assign attribute to attribute set */
$installer->addAttributeToGroup('catalog_product', 'Default', 'General', $dateAttribute->getId());

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product
    ->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setWebsiteIds([1])
    ->setName('Simple Products with date attribute')
    ->setSku('simple_product_with_date_attribute')
    ->setPrice(1)
    ->setCategoryIds([2])
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 5, 'is_in_stock' => 1])
    ->save();

$objectManager->get(Action::class)
    ->updateAttributes(
        [$product->getId()],
        [
            $dateAttribute->getAttributeCode() => '01/01/2000' // m/d/Y
        ],
        $product->getStoreId()
    );
