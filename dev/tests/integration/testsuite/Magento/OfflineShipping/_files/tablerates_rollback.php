<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\ResourceConnection;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$resource = $objectManager->get(ResourceConnection::class);
$connection = $resource->getConnection();
$resourceModel = $objectManager->create(Tablerate::class);
$entityTable = $resourceModel->getTable('shipping_tablerate');
$connection->query("DELETE FROM {$entityTable};");
