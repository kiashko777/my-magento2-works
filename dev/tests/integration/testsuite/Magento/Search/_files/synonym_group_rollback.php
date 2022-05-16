<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Search\Model\ResourceModel\SynonymGroup\Collection;
use Magento\Search\Model\SynonymGroupRepository;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$synonymGroupModel = $objectManager->get(Collection::class)->getLastItem();

$synonymGroupRepository = $objectManager->create(SynonymGroupRepository::class);
$synonymGroupRepository->delete($synonymGroupModel);
