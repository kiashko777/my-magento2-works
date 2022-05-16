<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $objectManager ObjectManager */

use Magento\Framework\Registry;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\Calculation\Rule;
use Magento\Tax\Model\ClassModel;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();
$customerTaxClass1 = $objectManager->create(
    ClassModel::class
)->setClassName(
    'CustomerTaxClass1'
)->setClassType(
    ClassModel::TAX_CLASS_TYPE_CUSTOMER
)->save();

$customerTaxClass2 = $objectManager->create(
    ClassModel::class
)->setClassName(
    'CustomerTaxClass2'
)->setClassType(
    ClassModel::TAX_CLASS_TYPE_CUSTOMER
)->save();

$productTaxClass1 = $objectManager->create(
    ClassModel::class
)->setClassName(
    'ProductTaxClass1'
)->setClassType(
    ClassModel::TAX_CLASS_TYPE_PRODUCT
)->save();

$productTaxClass2 = $objectManager->create(
    ClassModel::class
)->setClassName(
    'ProductTaxClass2'
)->setClassType(
    ClassModel::TAX_CLASS_TYPE_PRODUCT
)->save();

// Tax class created but not used in the rule to ensure that unused tax classes are handled properly
$productTaxClass3 = $objectManager->create(
    ClassModel::class
)->setClassName(
    'ProductTaxClass3'
)->setClassType(
    ClassModel::TAX_CLASS_TYPE_PRODUCT
)->save();

$taxRate = [
    'tax_country_id' => 'US',
    'tax_region_id' => '12',
    'tax_postcode' => '*',
    'code' => '*',
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
    'customer_tax_class_ids' => [$customerTaxClass1->getId(), $customerTaxClass2->getId()],
    'product_tax_class_ids' => [$productTaxClass1->getId(), $productTaxClass2->getId()],
    'tax_rate_ids' => [$rate->getId()],
    'tax_rates_codes' => [$rate->getId() => $rate->getCode()],
];

$taxRule = $objectManager->create(Rule::class)->setData($ruleData)->save();

$registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rule');
$registry->register('_fixture/Magento_Tax_Model_Calculation_Rule', $taxRule);

$ruleData['code'] = 'Test Rule Duplicate';

$objectManager->create(Rule::class)->setData($ruleData)->save();
