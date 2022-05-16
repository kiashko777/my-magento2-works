<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Create dummy user
 */

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;

Bootstrap::getInstance()
    ->loadArea(FrontNameResolver::AREA_CODE);
/** @var $user User */
$user = Bootstrap::getObjectManager()->create(User::class);
$user->setFirstname(
    'Dummy'
)->setLastname(
    'Dummy'
)->setEmail(
    'dummy@dummy.com'
)->setUsername(
    'dummy_username'
)->setPassword(
    'dummy_password1'
)->save();
