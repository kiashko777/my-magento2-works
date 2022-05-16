<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Security\Observer;

use Magento\Framework\Exception\Plugin\AuthenticationException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Security\Observer\AdminUserAuthenticateBefore
 */
class AdminUserAuthenticateBeforeTest extends TestCase
{

    /**
     * @magentoDataFixture Magento/Security/_files/expired_users.php
     */
    public function testWithExpiredUser()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later');

        $adminUserNameFromFixture = 'adminUserExpired';
        $password = \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD;
        /** @var User $user */
        $user = Bootstrap::getObjectManager()->create(User::class);
        $user->authenticate($adminUserNameFromFixture, $password);
        static::assertFalse((bool)$user->getIsActive());
    }

    /**
     * @magentoDataFixture Magento/Security/_files/expired_users.php
     */
    public function testWithNonExpiredUser()
    {
        $adminUserNameFromFixture = 'adminUserNotExpired';
        $password = \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD;
        /** @var User $user */
        $user = Bootstrap::getObjectManager()->create(User::class);
        $user->authenticate($adminUserNameFromFixture, $password);
        static::assertTrue((bool)$user->getIsActive());
    }
}
