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

Bootstrap::getInstance()
    ->loadArea(FrontNameResolver::AREA_CODE);
$user = Bootstrap::getObjectManager()->create(User::class);
$user->setFirstname(
    'CreateDate'
)->setLastname(
    'User 2'
)->setEmail(
    'dummy2@dummy.com'
)->setUsername(
    'user_created_date'
)->setPassword(
    'dummy_password2'
)->save();
$user = Bootstrap::getObjectManager()->create(User::class);
$user->loadByUsername('user_created_date');
$user->setCreated('2010-01-06 00:00:00');
$user->save();
