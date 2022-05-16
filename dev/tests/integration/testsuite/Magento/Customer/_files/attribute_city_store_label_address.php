<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
//@codingStandardsIgnoreFile
/** @var \Magento\Customer\Model\Attribute $model */

use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$model = Bootstrap::getObjectManager()->create(\Magento\Customer\Model\Attribute::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = Bootstrap::getObjectManager()->create(StoreManager::class);
$model->loadByCode('customer_address', 'city');
$storeLabels = $model->getStoreLabels();
$stores = $storeManager->getStores();
/** @var WebsiteInterface $website */
foreach ($stores as $store) {
    $storeLabels[$store->getId()] = 'Suburb';
}
$model->setStoreLabels($storeLabels);
$model->save();
