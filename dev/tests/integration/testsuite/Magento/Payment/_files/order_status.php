<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Status $status */

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\TestFramework\Helper\Bootstrap;

$status = Bootstrap::getObjectManager()->create(Status::class);
//status for state new
$status->setData('status', 'custom_new_status')->setData('label', 'Test Status')->save();
$status->assignState(Order::STATE_NEW, true);
//status for state canceled
$status->setData('status', 'custom_canceled_status')->setData('label', 'Test Status')->unsetData('id')->save();
$status->assignState(Order::STATE_CANCELED, true);
