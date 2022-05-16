<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Backend\Model\UrlInterface;
use Magento\TestFramework\Helper\Bootstrap;

Bootstrap::getObjectManager()->get(
    UrlInterface::class
)->turnOffSecretKey();
