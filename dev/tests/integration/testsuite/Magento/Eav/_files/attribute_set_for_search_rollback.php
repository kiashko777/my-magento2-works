<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$attributeSetData = [
    'attribute_set_1_for_search',
    'attribute_set_2_for_search',
    'attribute_set_3_for_search',
    'attribute_set_4_for_search',
];

foreach ($attributeSetData as $attributeSetName) {
    /** @var Set $attributeSet */
    $attributeSet = $objectManager->create(Set::class)
        ->load($attributeSetName, 'attribute_set_name');
    if ($attributeSet->getId()) {
        $attributeSet->delete();
    }
}
