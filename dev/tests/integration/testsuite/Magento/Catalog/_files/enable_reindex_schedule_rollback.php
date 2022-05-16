<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @var IndexerInterface $model */

use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\TestFramework\Helper\Bootstrap;

$model = Bootstrap::getObjectManager()->get(
    IndexerRegistry::class
)->get('catalogsearch_fulltext');
$model->setScheduled(false);
