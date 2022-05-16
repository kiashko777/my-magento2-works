<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Category\Attribute\Source\Mode;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$eavSetup = $objectManager->create(EavSetup::class);
$eavSetup->addAttribute(
    Product::ENTITY,
    'test_configurable_with_sm',
    [
        'group' => 'General',
        'type' => 'varchar',
        'backend' => '',
        'frontend' => '',
        'label' => 'Test configurable with source model',
        'input' => 'select',
        'source' => Mode::class,
        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'unique' => false,
        'apply_to' => ''
    ]
);

$eavConfig = $objectManager->get(Config::class);
$eavConfig->clear();
