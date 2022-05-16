<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @var Registry $registry */
/** @var Rule $salesRule */
/** @var RuleRepository $salesRuleRepository */

use Magento\Customer\Model\GroupManagement;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$registry = $objectManager->get(Registry::class);
$salesRule = $objectManager->create(Rule::class);
$salesRuleRepository = $objectManager->create(RuleRepository::class);
$allRules = $salesRuleRepository->getList($objectManager->get(SearchCriteriaInterface::class));
foreach ($allRules->getItems() as $rule) {
    $salesRuleRepository->deleteById($rule->getRuleId());
}
$salesRule->setData(
    [
        'name' => '50% off - July 4',
        'is_active' => 1,
        'customer_group_ids' => [GroupManagement::NOT_LOGGED_IN_ID],
        'coupon_type' => Rule::COUPON_TYPE_NO_COUPON,
        'simple_action' => 'by_percent',
        'discount_amount' => 50,
        'discount_step' => 0,
        'stop_rules_processing' => 1,
        'website_ids' => [
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getWebsite()->getId()
        ]
    ]
);
$salesRule->save();

$registry->unregister('Magento/SalesRule/_files/cart_rule_50_percent_off_no_condition/salesRuleId');
$registry->register('Magento/SalesRule/_files/cart_rule_50_percent_off_no_condition/salesRuleId', $salesRule->getId());
