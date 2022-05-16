<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */

use Magento\TestFramework\Helper\Bootstrap;

$attribute = Bootstrap::getObjectManager()
    ->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
$attribute->setAttributeCode('test_attribute_code_333')
    ->setEntityTypeId(4)
    ->setIsGlobal(1)
    ->setPrice(95)
    ->setIsUserDefined(1);
$attribute->save();
