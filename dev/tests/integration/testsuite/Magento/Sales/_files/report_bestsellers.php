<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// refresh report statistics
/** @var Bestsellers $reportResource */

use Magento\Sales\Model\ResourceModel\Report\Bestsellers;
use Magento\TestFramework\Helper\Bootstrap;

$reportResource = Bootstrap::getObjectManager()->create(
    Bestsellers::class
);
$reportResource->beginTransaction();
// prevent table truncation by incrementing the transaction nesting level counter
try {
    $reportResource->aggregate();
    $reportResource->commit();
} catch (Exception $e) {
    $reportResource->rollBack();
    throw $e;
}
