<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ObjectManagerInterface $objectManager */

use Magento\Customer\Model\AttributeFactory;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var AttributeFactory $attributeFactory */
$attributeFactory = $objectManager->create(AttributeFactory::class);

/** @var AttributeRepositoryInterface $attributeRepository */
$attributeRepository = $objectManager->create(AttributeRepositoryInterface::class);

/** @var CustomerSetup $setupResource */
$setupResource = $objectManager->create(CustomerSetup::class);

$attributeNames = ['custom_attribute1', 'custom_attribute2'];
foreach ($attributeNames as $attributeName) {
    /** @var \Magento\Customer\Model\Attribute $attribute */
    $attribute = $attributeFactory->create();

    $attribute->setName($attributeName)
        ->setEntityTypeId(2)
        ->setAttributeSetId(2)
        ->setAttributeGroupId(1)
        ->setFrontendInput('text')
        ->setFrontendLabel('custom_attribute_frontend_label')
        ->setIsUserDefined(true);

    $attributeRepository->save($attribute);

    $setupResource->getSetup()
        ->getConnection()
        ->insertMultiple(
            $setupResource->getSetup()->getTable('customer_form_attribute'),
            [['form_code' => 'customer_address_edit', 'attribute_id' => $attribute->getAttributeId()]]
        );
}
