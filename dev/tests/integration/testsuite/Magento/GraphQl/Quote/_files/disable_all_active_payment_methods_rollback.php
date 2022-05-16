<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @deprecated use @magentoConfigFixture instead.
 */
declare(strict_types=1);

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$rollbackConfigKey = 'test/payment/disabled_payment_methods';

$configWriter = $objectManager->create(WriterInterface::class);
$rollbackConfigValue = $objectManager->get(StoreManagerInterface::class)
    ->getStore(Store::DEFAULT_STORE_ID)
    ->getConfig($rollbackConfigKey);

$disabledPaymentMethods = [];
if (!empty($rollbackConfigValue)) {
    $disabledPaymentMethods = explode(',', $rollbackConfigValue);
}

if (count($disabledPaymentMethods)) {
    foreach ($disabledPaymentMethods as $keyToRemove) {
        $configWriter->delete(sprintf('payment/%s/active', $keyToRemove));
    }
}
$configWriter->delete($rollbackConfigKey);

$scopeConfig = $objectManager->get(ScopeConfigInterface::class);
$scopeConfig->clean();
