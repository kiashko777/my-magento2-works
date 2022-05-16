<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\UrlRewrite\Model\UrlRewrite;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$website = Bootstrap::getObjectManager()->create(Website::class);
/** @var $website Website */
$websiteId = $website->load('secondwebsite', 'code')->getId();
if ($websiteId) {
    $website->delete();
}
$store = Bootstrap::getObjectManager()->create(Store::class);
if ($store->load('secondstore', 'code')->getId()) {
    $store->delete();
}

/** @var $website Website */
$websiteId = $website->load('thirdwebsite', 'code')->getId();
if ($websiteId) {
    $website->delete();
}

$store = Bootstrap::getObjectManager()->create(Store::class);
if ($store->load('thirdstore', 'code')->getId()) {
    $store->delete();
}

$urlRewriteCollectionFactory = Bootstrap::getObjectManager()->get(
    UrlRewriteCollectionFactory::class
);
/** @var UrlRewriteCollection $urlRewriteCollection */
$urlRewriteCollection = $urlRewriteCollectionFactory->create();
$urlRewriteCollection->addFieldToFilter('store_id', ['gt' => 1]);
$urlRewrites = $urlRewriteCollection->getItems();
/** @var UrlRewrite $urlRewrite */
foreach ($urlRewrites as $urlRewrite) {
    try {
        $urlRewrite->delete();
    } catch (Exception $exception) {
        // already removed
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
