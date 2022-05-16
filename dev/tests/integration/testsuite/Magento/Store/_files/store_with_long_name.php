<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var $store Store */

use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;

$store = Bootstrap::getObjectManager()->create(Store::class);
$storeName = str_repeat('a', 255);
if (!$store->load('test', 'code')->getId()) {
    $store->setData(
        [
            'code' => 'test_2',
            'website_id' => '1',
            'group_id' => '1',
            'name' => $storeName,
            'sort_order' => '10',
            'is_active' => '1',
        ]
    );
    $store->save();
} else {
    if ($store->getId()) {
        /** @var Bootstrap $registry */
        $registry = Bootstrap::getObjectManager()->get(
            Registry::class
        );
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        $store->delete();

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);

        $store = Bootstrap::getObjectManager()->create(Store::class);
        $store->setData(
            [
                'code' => 'test_2',
                'website_id' => '1',
                'group_id' => '1',
                'name' => $storeName,
                'sort_order' => '10',
                'is_active' => '1',
            ]
        );
        $store->save();
    }
}
