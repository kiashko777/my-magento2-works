<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Group;
use Magento\Framework\Registry;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\Calculation\Rule;
use Magento\Tax\Model\ClassModel;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$customerTaxClass = $objectManager->create(
    ClassModel::class
)->setClassName(
    'CustomerTaxClass'
)->setClassType(
    ClassModel::TAX_CLASS_TYPE_CUSTOMER
)->save();

/** @var Customer $customer */
$customer = $objectManager->create(Customer::class)->load(1);
/** @var Group $customerGroup */
$customerGroup = $objectManager->create(Group::class)
    ->load('custom_group', 'customer_group_code');
$customerGroup->setTaxClassId($customerTaxClass->getId())->save();
$customer->setGroupId($customerGroup->getId())->save();

$taxRate = [
    'tax_country_id' => 'US',
    'tax_region_id' => '1',
    'tax_postcode' => '75477',
    'code' => 'US-AL-*-Rate-1',
    'rate' => '7.5',
];
$rate = $objectManager->create(Rate::class)->setData($taxRate)->save();

/** @var Magento\Framework\Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rate');
$registry->register('_fixture/Magento_Tax_Model_Calculation_Rate', $rate);

$ruleData = [
    'code' => 'Test Rule',
    'priority' => '0',
    'position' => '0',
    'customer_tax_class_ids' => [$customerTaxClass->getId()],
    'product_tax_class_ids' => [2],
    'tax_rate_ids' => [$rate->getId()],
    'tax_rates_codes' => [$rate->getId() => $rate->getCode()],
];

$taxRule = $objectManager->create(Rule::class)->setData($ruleData)->save();

$registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rule');
$registry->register('_fixture/Magento_Tax_Model_Calculation_Rule', $taxRule);
