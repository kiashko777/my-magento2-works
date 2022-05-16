<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Create an admin user with an assigned role
 */

/** @var $model User */

use Magento\TestFramework\Bootstrap;
use Magento\User\Model\User;

$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(User::class);
$model->setFirstname("John")
    ->setLastname("Doe")
    ->setUsername('adminUser')
    ->setPassword(Bootstrap::ADMIN_PASSWORD)
    ->setEmail('adminUser@example.com')
    ->setRoleType('G')
    ->setResourceId('Magento_Backend::all')
    ->setPrivileges("")
    ->setAssertId(0)
    ->setRoleId(1)
    ->setPermission('allow');
$model->save();
