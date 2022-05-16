<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $cacheTypeList TypeListInterface */

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\TestFramework\Helper\Bootstrap;

$cacheTypeList = Bootstrap::getObjectManager()->create(
    TypeListInterface::class
);
$cacheTypeList->invalidate(array_keys($cacheTypeList->getTypes()));
