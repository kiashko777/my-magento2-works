<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;

$objectManager = Bootstrap::getObjectManager();

$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$urlRewriteCollection = $objectManager->create(UrlRewriteCollection::class);
$collection = $urlRewriteCollection
    ->addFieldToFilter('target_path', ['test_page1', 'test_page2'])
    ->load()
    ->walk('delete');

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
