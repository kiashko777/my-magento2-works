<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/* @var IndexerInterface $model */

use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\TestFramework\Helper\Bootstrap;

$model = Bootstrap::getObjectManager()->get(
    IndexerRegistry::class
)->get('catalog_category_product');
$model->setScheduled(true);

$model = Bootstrap::getObjectManager()->get(
    IndexerRegistry::class
)->get('catalog_product_category');
$model->setScheduled(true);
