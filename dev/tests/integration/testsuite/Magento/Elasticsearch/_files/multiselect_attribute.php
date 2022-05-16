<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var CategorySetup $installer */
$installer = Bootstrap::getObjectManager()->create(CategorySetup::class);

/** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
$multiselectAttribute = $objectManager->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$multiselectAttribute->setData(
    [
        'attribute_code' => 'multiselect_attribute',
        'entity_type_id' => $installer->getEntityTypeId('catalog_product'),
        'is_global' => 1,
        'is_user_defined' => 1,
        'frontend_input' => 'multiselect',
        'is_unique' => 0,
        'is_required' => 0,
        'is_searchable' => 1,
        'is_visible_in_advanced_search' => 0,
        'is_comparable' => 0,
        'is_filterable' => 1,
        'is_filterable_in_search' => 0,
        'is_used_for_promo_rules' => 0,
        'is_html_allowed_on_front' => 1,
        'is_visible_on_front' => 0,
        'used_in_product_listing' => 0,
        'used_for_sort_by' => 0,
        'frontend_label' => ['Multiselect Attribute'],
        'backend_type' => 'varchar',
        'backend_model' => ArrayBackend::class,
        'option' => [
            'value' => [
                'dog' => ['Dog'],
                'cat' => ['Cat'],
            ],
            'order' => [
                'dog' => 1,
                'cat' => 2,
            ],
        ],
    ]
);
$multiselectAttribute->save();

$installer->addAttributeToGroup(
    'catalog_product',
    'Default',
    'General',
    $multiselectAttribute->getId()
);
