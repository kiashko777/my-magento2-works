<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Magento\Framework\Registry $registry */

use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

/** @var Magento\SalesRule\Model\Rule $rule */
$rule = $registry->registry('cart_rule_free_shipping');
if ($rule) {
    $rule->delete();
}
