<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
$attribute = Bootstrap::getObjectManager()
    ->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
$attribute->load('fixed_product_attribute', 'attribute_code');
if ($attribute->getId()) {
    $attribute->delete();
}

Resolver::getInstance()->requireDataFixture('Magento/Weee/_files/product_with_fpt_rollback.php');

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
