<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var Registry $registry */
/** @var Rule $salesRule */

use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$registry = $objectManager->get(Registry::class);
$salesRule = $objectManager->create(Rule::class);
$salesRuleId = $registry->registry('Magento/SalesRule/_files/cart_rule_50_percent_off_no_condition/salesRuleId');
if ($salesRuleId) {
    $salesRule->load($salesRuleId);
    if ($salesRule->getId()) {
        $salesRule->delete();
    }
}
