<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @var Registry $registry */

use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
$attribute = Bootstrap::getObjectManager()
    ->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);

$attribute->loadByCode(4, 'test_configurable');

if ($attribute->getId()) {
    $attribute->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
