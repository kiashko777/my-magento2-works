<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Authorization\Model;

use Magento\TestFramework\Entity;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class RulesTest extends TestCase
{
    /**
     * @var Rules
     */
    protected $_model;

    /**
     * @var User
     */
    protected $user;

    /**
     * @magentoDbIsolation enabled
     */
    public function testCRUD()
    {
        $this->_model
            ->setRoleType('G')
            ->setResourceId('Magento_Backend::all')
            ->setPrivileges("")
            ->setAssertId(0)
            ->setRoleId(1)
            ->setPermission('allow');

        $crud = new Entity($this->_model, ['permission' => 'deny']);
        $crud->testCrud();
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testInitialUserPermissions()
    {
        $expectedDefaultPermissions = ['Magento_Backend::all'];
        $this->_checkExistingPermissions($expectedDefaultPermissions);
    }

    /**
     * Ensure that only expected permissions are set.
     */
    protected function _checkExistingPermissions($expectedDefaultPermissions)
    {
        $connection = $this->_model->getResource()->getConnection();
        $this->user->loadByUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);
        $roleId = $this->user->getRole()->getRoleId();
        $ruleSelect = $connection->select()
            ->from($this->_model->getResource()->getMainTable())
            ->where('role_id = ?', $roleId);

        $rules = $ruleSelect->query()->fetchAll();
        $actualPermissions = [];
        foreach ($rules as $rule) {
            $actualPermissions[] = $rule['resource_id'];
            $this->assertEquals(
                'allow',
                $rule['permission'],
                "Permission for '{$rule['resource_id']}' resource should be 'allow'"
            );
        }
        $this->assertEquals($expectedDefaultPermissions, $actualPermissions, 'Default permissions are invalid');
    }

    /**
     * @covers \Magento\Authorization\Model\Rules::saveRel
     * @magentoDbIsolation enabled
     */
    public function testSetAllowForAllResources()
    {
        $resources = ['Magento_Backend::all'];
        $this->user->loadByUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);
        $roleId = $this->user->getRole()->getRoleId();
        $this->_model->setRoleId($roleId)->setResources($resources)->saveRel();
        $expectedPermissions = ['Magento_Backend::all'];
        $this->_checkExistingPermissions($expectedPermissions);
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Rules::class
        );
        $this->user = Bootstrap::getObjectManager()->create(
            User::class
        );
    }
}
