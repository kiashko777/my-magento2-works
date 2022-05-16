<?php
declare(strict_types=1);
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Security\Model\UserExpiration;
use Magento\TestFramework\Bootstrap;
use Magento\User\Model\User;

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/**
 * Create an admin user with expired and non-expired access date
 */

/** @var $userModelNotExpired User */
$userModelNotExpired = $objectManager->create(User::class);
$userModelNotExpired->setFirstName("John")
    ->setLastName("Doe")
    ->setUserName('adminUserNotExpired')
    ->setPassword(Bootstrap::ADMIN_PASSWORD)
    ->setEmail('adminUserNotExpired@example.com')
    ->setRoleType('G')
    ->setResourceId('Magento_Adminhtml::all')
    ->setPrivileges("")
    ->setAssertId(0)
    ->setRoleId(1)
    ->setPermission('allow')
    ->setIsActive(1)
    ->save();
$futureDate = new DateTime();
$futureDate->modify('+10 days');
$notExpiredRecord = $objectManager->create(UserExpiration::class);
$notExpiredRecord
    ->setId($userModelNotExpired->getId())
    ->setExpiresAt($futureDate->format('Y-m-d H:i:s'))
    ->save();

/** @var $userModelExpired User */
$pastDate = new DateTime();
$pastDate->modify('-10 days');
$userModelExpired = $objectManager->create(User::class);
$userModelExpired->setFirstName("John")
    ->setLastName("Doe")
    ->setUserName('adminUserExpired')
    ->setPassword(Bootstrap::ADMIN_PASSWORD)
    ->setEmail('adminUserExpired@example.com')
    ->setRoleType('G')
    ->setResourceId('Magento_Adminhtml::all')
    ->setPrivileges("")
    ->setAssertId(0)
    ->setRoleId(1)
    ->setPermission('allow')
    ->setIsActive(1)
    ->save();
$expiredRecord = $objectManager->create(UserExpiration::class);
$expiredRecord
    ->setId($userModelExpired->getId())
    ->setExpiresAt($pastDate->format('Y-m-d H:i:s'))
    ->save();
