<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Bootstrap::getInstance()->loadArea('Adminhtml');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/category.php');
