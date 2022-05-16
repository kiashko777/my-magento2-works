<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Security\Model\AdminSessionInfo;
use Magento\TestFramework\Helper\Bootstrap;

$om = Bootstrap::getObjectManager();
$session = $om->create(AdminSessionInfo::class);
$session->setSessionId('569e2e3d752e9')
    ->setUserId(1)
    ->setStatus(AdminSessionInfo::LOGGED_IN)
    ->setCreatedAt('2016-01-19 15:42:13')
    ->setUpdatedAt('2016-01-19 15:42:13')
    ->save();

$session = $om->create(AdminSessionInfo::class);
$session->setSessionId('569e2277752e9')
    ->setUserId(1)
    ->setStatus(AdminSessionInfo::LOGGED_IN)
    ->setCreatedAt('2016-01-18 13:00:13')
    ->setUpdatedAt('2016-01-18 13:00:13')
    ->save();
