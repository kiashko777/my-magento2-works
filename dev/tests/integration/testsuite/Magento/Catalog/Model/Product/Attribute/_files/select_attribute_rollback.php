<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */

use Magento\TestFramework\Helper\Bootstrap;

$attribute = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->load('select_attribute', 'attribute_code');
if ($attribute->getId()) {
    $attribute->delete();
}
