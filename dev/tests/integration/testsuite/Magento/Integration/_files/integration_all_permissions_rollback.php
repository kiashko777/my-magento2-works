<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $integration Integration */

use Magento\Integration\Model\Integration;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$integration = $objectManager->create(Integration::class);
$integration->load('Fixture Integration', 'name');
if ($integration->getId()) {
    $integration->delete();
}
