<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Helper\Bootstrap;

$model = Bootstrap::getObjectManager()->create(\Magento\Customer\Model\Attribute::class);
$model->load('address_user_attribute', 'attribute_code')->delete();
