<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var $objectManager ObjectManager */

use Magento\Customer\Model\Group;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\Calculation\RateFactory;
use Magento\Tax\Model\Calculation\RateRepository;
use Magento\Tax\Model\Calculation\Rule;
use Magento\Tax\Model\ClassModel;
use Magento\Tax\Model\ResourceModel\TaxClass;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();

$taxRuleResource = $objectManager->get(\Magento\Tax\Model\ResourceModel\Calculation\Rule::class);
$taxRule = $objectManager->create(Rule::class);
$taxRuleResource->load($taxRule, 'Test Rule', 'code');
$taxRuleResource->delete($taxRule);

/** @var TaxClass $resourceModel */
$resourceModel = $objectManager->get(TaxClass::class);

$customerGroup = $objectManager->create(Group::class)
    ->load('custom_group', 'customer_group_code');
$customerGroup->setTaxClassId(3)->save();

try {
    /** @var ClassModel $taxClassEntity */
    $taxClassEntity = $objectManager->create(ClassModel::class);
    $resourceModel->load($taxClassEntity, 'CustomerTaxClass', 'class_name');
    $resourceModel->delete($taxClassEntity);
} catch (CouldNotDeleteException $couldNotDeleteException) {
    // It's okay if the entity already wiped from the database
}

/** @var Rate $rate */
$rate = $objectManager->get(RateFactory::class)->create();
/** @var RateRepository $rateRepository */
$rateRepository = $objectManager->get(RateRepository::class);
$rate->loadByCode('US-AL-*-Rate-1');
if ($rate->getId()) {
    $rateRepository->delete($rate);
}
