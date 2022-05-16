<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\Registry;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as RuleCollection;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var RuleCollection $collection */
$collection = Bootstrap::getObjectManager()->create(RuleCollection::class);
$collection->addFieldToFilter('name', 'Rule with coupon list');
$rule = $collection->getFirstItem();
if ($rule->getId()) {
    $rule->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
