<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Authorization\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class RoleTest extends TestCase
{
    /**
     * @var Role
     */
    protected $_model = null;

    public function testGetRoleUsers()
    {
        $this->assertEmpty($this->_model->getRoleUsers());

        $this->_model->load(\Magento\TestFramework\Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        $this->assertNotEmpty($this->_model->getRoleUsers());
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()
            ->create(Role::class);
    }
}
