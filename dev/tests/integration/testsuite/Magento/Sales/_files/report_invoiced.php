<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// refresh report statistics
/** @var Invoiced $reportResource */

use Magento\Sales\Model\ResourceModel\Report\Invoiced;
use Magento\TestFramework\Helper\Bootstrap;

$reportResource = Bootstrap::getObjectManager()->create(
    Invoiced::class
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
