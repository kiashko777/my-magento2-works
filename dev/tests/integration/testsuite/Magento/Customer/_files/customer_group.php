<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @var GroupRepositoryInterface $groupRepository */

use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

$groupRepository = Bootstrap::getObjectManager()->create(
    GroupRepositoryInterface::class
);

$groupFactory = Bootstrap::getObjectManager()->create(
    GroupInterfaceFactory::class
);
$groupDataObject = $groupFactory->create();
$groupDataObject->setCode('custom_group')->setTaxClassId(3);
$groupRepository->save($groupDataObject);
