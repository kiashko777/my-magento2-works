<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Set $attributeSet */
$attributeSet = $objectManager->create(Set::class)
    ->load('Super Powerful Muffins', 'attribute_set_name');
if ($attributeSet->getId()) {
    $attributeSet->delete();
}

$attributeSet = $objectManager->create(Set::class)
    ->load('Banana Rangers', 'attribute_set_name');
if ($attributeSet->getId()) {
    $attributeSet->delete();
}

$attributeSet = $objectManager->create(Set::class)
    ->load('Guardians of the Refrigerator', 'attribute_set_name');
if ($attributeSet->getId()) {
    $attributeSet->delete();
}
