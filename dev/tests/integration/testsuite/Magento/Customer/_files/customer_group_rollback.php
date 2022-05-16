<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var ObjectManagerInterface $objectManager */
$objectManager = Bootstrap::getObjectManager();
$groupRepository = $objectManager->get(GroupRepositoryInterface::class);
/** @var SearchCriteriaBuilder $searchBuilder */
$searchBuilder = $objectManager->get(SearchCriteriaBuilder::class);
$searchCriteria = $searchBuilder->addFilter(GroupInterface::CODE, 'custom_group')
    ->create();
$groups = $groupRepository->getList($searchCriteria)
    ->getItems();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
foreach ($groups as $group) {
    try {
        $groupRepository->delete($group);
    } catch (NoSuchEntityException $exception) {
        //Group already removed
    }
}
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
