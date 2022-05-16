<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Helper\Bootstrap;

$model = Bootstrap::getObjectManager()->create(\Magento\Customer\Model\Attribute::class);
$model->loadByCode('customer', 'prefix')->setIsVisible('1');
$model->save();

$model->loadByCode('customer', 'middlename')->setIsVisible('1');
$model->save();

$model->loadByCode('customer', 'suffix')->setIsVisible('1');
$model->save();
