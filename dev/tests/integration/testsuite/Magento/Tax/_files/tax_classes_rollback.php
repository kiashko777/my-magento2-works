<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var $objectManager ObjectManager */

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Tax\Model\Calculation\Rule;
use Magento\Tax\Model\ClassModel;
use Magento\Tax\Model\ResourceModel\TaxClass;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();

$taxRules = [
    'Test Rule',
    'Test Rule Duplicate',
];
$taxClasses = [
    'ProductTaxClass1',
    'ProductTaxClass2',
    'ProductTaxClass3',
    'CustomerTaxClass1',
    'CustomerTaxClass2',
];


$taxRuleResource = $objectManager->get(\Magento\Tax\Model\ResourceModel\Calculation\Rule::class);
foreach ($taxRules as $taxRuleCode) {
    $taxRule = $objectManager->create(Rule::class);
    $taxRuleResource->load($taxRule, $taxRuleCode, 'code');
    $taxRuleResource->delete($taxRule);
}

/** @var TaxClass $resourceModel */
$resourceModel = $objectManager->get(TaxClass::class);

foreach ($taxClasses as $taxClass) {
    try {
        /** @var ClassModel $taxClassEntity */
        $taxClassEntity = $objectManager->create(ClassModel::class);
        $resourceModel->load($taxClassEntity, $taxClass, 'class_name');
        $resourceModel->delete($taxClassEntity);
    } catch (CouldNotDeleteException $couldNotDeleteException) {
        // It's okay if the entity already wiped from the database
    }
}
