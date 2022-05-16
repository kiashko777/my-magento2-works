<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $cachePool \Magento\Framework\App\Cache\Frontend\Pool */

use Magento\Framework\Cache\FrontendInterface;
use Magento\TestFramework\Helper\Bootstrap;

$cachePool = Bootstrap::getObjectManager()
    ->create(\Magento\Framework\App\Cache\Frontend\Pool::class);
/** @var $cacheFrontend FrontendInterface */
foreach ($cachePool as $cacheFrontend) {
    $cacheFrontend->getBackend()->save('non-application cache data', 'NON_APPLICATION_FIXTURE', ['SOME_TAG']);
}
