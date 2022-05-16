<?php
/**
 * Entry point for static resources (JS, CSS, etc.)
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\StaticResource;

require realpath(__DIR__) . '/../app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
/** @var StaticResource $app */
$app = $bootstrap->createApplication(StaticResource::class);
$bootstrap->run($app);
