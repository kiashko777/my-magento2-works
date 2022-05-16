<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Search\Model\Query;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var $query Query */
$query = $objectManager->create(Query::class);
$query->setStoreId(1);
$query->setQueryText(
    'popular_query_text'
)->setNumResults(
    1
)->setPopularity(
    100
)->setDisplayInTerms(
    1
)->setIsActive(
    1
)->setIsProcessed(
    1
)->save();
