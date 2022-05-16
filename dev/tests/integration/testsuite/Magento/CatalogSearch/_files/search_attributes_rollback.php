<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Registry $registry */

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$eavConfig = Bootstrap::getObjectManager()->get(Config::class);
$attributesCode = ['test_advanced_search', 'test_quick_search', 'test_catalog_view'];

foreach (['test_quick_search', 'test_catalog_view'] as $code) {
    $attribute = $eavConfig->getAttribute('catalog_product', $code);
    if ($attribute instanceof AbstractAttribute
        && $attribute->getId()
    ) {
        $attribute->delete();
    }
}
$eavConfig->clear();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
