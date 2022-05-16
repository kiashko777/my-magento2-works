<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var CategorySetup $installer */

use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\Config;
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
        'attribute_code' => 'attribute_with_invalid_applyto',
        'entity_type_id' => $installer->getEntityTypeId('catalog_product'),
        'apply_to' => 'invalid-type',
    ]
);
$attribute->save();

/* Assign attribute to attribute set */
$installer->addAttributeToGroup('catalog_product', 'Default', 'General', $attribute->getId());

/** @var Config $eavConfig */
$eavConfig = Bootstrap::getObjectManager()->get(Config::class);
$eavConfig->clear();
