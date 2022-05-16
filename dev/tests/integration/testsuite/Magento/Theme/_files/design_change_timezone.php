<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Design;

$designChanges = [
    ['store' => 'default', 'design' => 'default_yesterday_design', 'date' => '-1 day'],
    ['store' => 'default', 'design' => 'default_today_design', 'date' => 'now'],
    ['store' => 'default', 'design' => 'default_tomorrow_design', 'date' => '+1 day'],
    ['store' => 'admin', 'design' => 'admin_yesterday_design', 'date' => '-1 day'],
    ['store' => 'admin', 'design' => 'admin_today_design', 'date' => 'now'],
    ['store' => 'admin', 'design' => 'admin_tomorrow_design', 'date' => '+1 day'],
];
foreach ($designChanges as $designChangeData) {
    $storeId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getStore(
        $designChangeData['store']
    )->getId();
    $change = Bootstrap::getObjectManager()->create(Design::class);
    $change->setStoreId(
        $storeId
    )->setDesign(
        $designChangeData['design']
    )->setDateFrom(
        $designChangeData['date']
    )->setDateTo(
        $designChangeData['date']
    )->save();
}
