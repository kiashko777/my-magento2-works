<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Status $status */

use Magento\Sales\Model\Order\Status;
use Magento\TestFramework\Helper\Bootstrap;

$status = Bootstrap::getObjectManager()->create(
    Status::class
);
$status->setData(
    [
        'status' => 'fake_status_do_not_use_it',
        'label' => 'Fake status do not use it',
    ]
);
$status->save();
$status->assignState('fake_state_do_not_use_it', true, true);
