<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$website = $objectManager->get(Magento\Store\Model\Website::class);
$website->load('test_website', 'code');

if (!$website->getId()) {
    /** @var Magento\Store\Model\Website $website */
    $website->setData(
        [
            'code' => 'test_website',
            'name' => 'Test Website',
        ]
    );

    $website->save();
}

$objectManager->get(StoreManagerInterface::class)->reinitStores();
