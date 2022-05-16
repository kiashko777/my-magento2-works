<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Model\Customer;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Magento\Framework\Indexer\IndexerRegistry $indexerRegistry */
$indexerRegistry = $objectManager->create(IndexerRegistry::class);
$indexer = $indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
$indexer->setScheduled(false);
