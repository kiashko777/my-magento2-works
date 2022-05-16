<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @var $resource Magento\Framework\App\ResourceConnection
 */
$resource = Bootstrap::getObjectManager()->get(ResourceConnection::class);
$connection = $resource->getConnection();
$bulkTable = $resource->getTableName('magento_bulk');

$connection->query("DELETE FROM {$bulkTable};");
