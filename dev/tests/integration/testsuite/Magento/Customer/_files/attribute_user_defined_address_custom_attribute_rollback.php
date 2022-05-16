<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var \Magento\Customer\Model\Attribute $attributeModel */

use Magento\TestFramework\Helper\Bootstrap;

$attributeModel = Bootstrap::getObjectManager()->create(
    \Magento\Customer\Model\Attribute::class
);
$attributeModel->load('custom_attribute1', 'attribute_code')->delete();
$attributeModel->load('custom_attribute2', 'attribute_code')->delete();
