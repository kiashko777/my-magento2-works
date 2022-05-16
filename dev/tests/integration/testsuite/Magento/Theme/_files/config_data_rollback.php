<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var ResourceConnection $resource */
$resource = $objectManager->get(ResourceConnection::class);
$connection = $resource->getConnection();
$tableName = $resource->getTableName('core_config_data');

$connection->query("DELETE FROM $tableName WHERE path = 'design/header/welcome';");
