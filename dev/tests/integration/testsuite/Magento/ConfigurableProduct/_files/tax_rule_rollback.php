<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var Registry $registry */

use Magento\Framework\Registry;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\Calculation\Rule;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
/** @var Magento\Framework\Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rule');
$objectManager->create(Rule::class)->load('Test Rule', 'code')->delete();

$registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rate');
$objectManager->create(Rate::class)->loadByCode('*')->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
