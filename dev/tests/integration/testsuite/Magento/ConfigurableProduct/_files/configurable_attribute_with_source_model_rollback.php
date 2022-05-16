<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$eavConfig = $objectManager->get(Config::class);
$attribute = $eavConfig->getAttribute(Product::ENTITY, 'test_configurable_with_sm');
if ($attribute instanceof AbstractAttribute && $attribute->getId()) {
    $attribute->delete();
}
$eavConfig->clear();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
