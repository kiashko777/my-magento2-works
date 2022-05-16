<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Set $attributeSet */
$attributeSet = $objectManager->create(Set::class)
    ->load('empty_attribute_set', 'attribute_set_name');
if ($attributeSet->getId()) {
    $attributeSet->delete();
}
