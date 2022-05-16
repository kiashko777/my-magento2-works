<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Helper\Bootstrap;

$model = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$model->setName('user_attribute')->setId(1)->setEntityTypeId(4)->setIsUserDefined(1);
$model->save();
