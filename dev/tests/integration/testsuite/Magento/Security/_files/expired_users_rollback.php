<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;

/** @var ObjectManagerInterface $objectManager */
$objectManager = Bootstrap::getObjectManager();
$userFactory = $objectManager->get(UserFactory::class);
$userNames = ['adminUserNotExpired', 'adminUserExpired'];

foreach ($userNames as $userName) {
    /** @var User $user */
    $user = $userFactory->create();
    $user->load($userName, 'username');

    if ($user->getId() !== null) {
        $user->delete();
    }
}
