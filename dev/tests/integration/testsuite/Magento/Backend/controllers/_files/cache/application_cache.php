<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $cache Cache */

use Magento\Framework\App\Cache;
use Magento\TestFramework\Helper\Bootstrap;

$cache = Bootstrap::getObjectManager()->create(Cache::class);
$cache->save('application data', 'APPLICATION_FIXTURE');
