<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Authorization\Model\ResourceModel;

use Magento\TestFramework\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Role resource test
 *
 * @magentoAppArea Adminhtml
 */
class RoleTest extends TestCase
{
    public function testGetRoleUsers()
    {
        $role = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Authorization\Model\Role::class
        );
        $roleResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            Role::class
        );

        $this->assertEmpty($roleResource->getRoleUsers($role));

        $role->load(Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        $this->assertNotEmpty($roleResource->getRoleUsers($role));
    }
}
