<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Layout $layoutCache */

use Magento\Framework\App\Cache\Type\Layout;
use Magento\TestFramework\Helper\Bootstrap;

$layoutCache = Bootstrap::getObjectManager()
    ->get(Layout::class);
$layoutCache->save('fixture layout cache data', 'LAYOUT_CACHE_FIXTURE');
