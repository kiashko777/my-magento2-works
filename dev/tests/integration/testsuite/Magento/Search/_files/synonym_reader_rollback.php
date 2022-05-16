<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ResourceConnection $resource */

use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\Helper\Bootstrap;

$resource = Bootstrap::getObjectManager()
    ->get(ResourceConnection::class);

$connection = $resource->getConnection('default');
$connection->truncateTable($resource->getTableName('search_synonyms'));
