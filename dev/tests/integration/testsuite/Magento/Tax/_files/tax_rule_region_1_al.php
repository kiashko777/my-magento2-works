<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $objectManager ObjectManager */

use Magento\Framework\Registry;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\Calculation\Rule;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();

$taxRate = [
    'tax_country_id' => 'US',
    'tax_region_id' => '1',
    'tax_postcode' => '*',
    'code' => 'US-AL-*-Rate-1',
    'rate' => '7.5',
];
$rate = $objectManager->create(Rate::class)->setData($taxRate)->save();

/** @var Magento\Framework\Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rate');
$registry->register('_fixture/Magento_Tax_Model_Calculation_Rate', $rate);
//$registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rate_AL');
//$registry->register('_fixture/Magento_Tax_Model_Calculation_Rate_NY_AL', $rate);

$ruleData = [
    'code' => 'AL Test Rule',
    'priority' => '0',
    'position' => '0',
    'customer_tax_class_ids' => [3],
    'product_tax_class_ids' => [2],
    'tax_rate_ids' => [$rate->getId()],
    'tax_rates_codes' => [$rate->getId() => $rate->getCode()],
];

$taxRule = $objectManager->create(Rule::class)->setData($ruleData)->save();
$registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rule');
$registry->register('_fixture/Magento_Tax_Model_Calculation_Rule', $taxRule);
