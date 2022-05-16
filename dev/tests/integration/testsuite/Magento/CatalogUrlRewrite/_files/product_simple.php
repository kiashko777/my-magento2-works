<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Bootstrap::getInstance()
    ->loadArea(FrontNameResolver::AREA_CODE);

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');
