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
$customerTaxClass = $objectManager->create(ClassModel::class)->load('Retail Customer', 'class_name');
$productTaxClass1 = $objectManager->create(ClassModel::class)->load('Taxable Goods', 'class_name');

$taxRate = [
    'tax_country_id' => 'US',
    'tax_region_id' => '0',
    'tax_postcode' => '*',
    'code' => '*',
    'rate' => '10',
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
    'product_tax_class_ids' => [$productTaxClass1->getId()],
    'tax_rate_ids' => [$rate->getId()],
];

$taxRule = $objectManager->create(Rule::class)->setData($ruleData)->save();

$registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rule');
$registry->register('_fixture/Magento_Tax_Model_Calculation_Rule', $taxRule);
