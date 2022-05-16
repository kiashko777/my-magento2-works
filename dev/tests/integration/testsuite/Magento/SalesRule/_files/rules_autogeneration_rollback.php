<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $objectManager ObjectManager */

use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();
/** @var Magento\Framework\Registry $registry */
$registry = $objectManager->get(Registry::class);
/** @var $salesRule Rule */
$salesRule = $registry->registry('_fixture/Magento_SalesRule_Api_RuleRepository');
$salesRule->delete();
$registry->unregister('_fixture/Magento_SalesRule_Api_RuleRepository');
