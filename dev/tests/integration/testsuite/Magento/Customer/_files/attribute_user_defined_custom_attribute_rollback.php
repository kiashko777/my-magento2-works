<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Helper\Bootstrap;

$model = Bootstrap::getObjectManager()->create(\Magento\Customer\Model\Attribute::class);
$model->load('custom_attribute1', 'attribute_code')->delete();

$model2 = Bootstrap::getObjectManager()->create(\Magento\Customer\Model\Attribute::class);
$model2->load('custom_attribute2', 'attribute_code')->delete();

$model2 = Bootstrap::getObjectManager()->create(\Magento\Customer\Model\Attribute::class);
$model2->load('customer_image', 'attribute_code')->delete();
