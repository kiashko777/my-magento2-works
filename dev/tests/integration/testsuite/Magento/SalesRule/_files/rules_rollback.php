<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\SalesRule\Model\ResourceModel\Rule\Collection;
use Magento\SalesRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;

$collection = Bootstrap::getObjectManager()
    ->get(Collection::class);

/** @var Rule $rule */
foreach ($collection as $rule) {
    $rule->delete();
}
