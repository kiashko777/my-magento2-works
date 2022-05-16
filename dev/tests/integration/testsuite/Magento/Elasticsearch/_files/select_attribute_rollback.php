<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
/** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
$attribute = $objectManager->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->load('select_attribute', 'attribute_code');
$attribute->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
