<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Indexer\Model\Indexer;
use Magento\TestFramework\Helper\Bootstrap;

$indexer = Bootstrap::getObjectManager()->create(
    Indexer::class
);
$indexer->load('catalogsearch_fulltext');
$indexer->reindexAll();
