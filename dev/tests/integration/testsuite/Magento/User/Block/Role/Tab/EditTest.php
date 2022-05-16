<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\User\Block\Role\Tab;

use Magento\Authorization\Model\Role;
use Magento\Framework\App\RequestInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class EditTest extends TestCase
{
    /**
     * @var Edit
     */
    protected $_block;

    public function testConstructor()
    {
        $this->assertNotEmpty($this->_block->getSelectedResources());
        $this->assertContains('Magento_Backend::all', $this->_block->getSelectedResources());
    }

    public function testGetTree()
    {
        $encodedTree = $this->_block->getTree();
        $this->assertNotEmpty($encodedTree);
    }

    protected function setUp(): void
    {
        $roleAdmin = Bootstrap::getObjectManager()
            ->create(Role::class);
        $roleAdmin->load(\Magento\TestFramework\Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        Bootstrap::getObjectManager()->get(
            RequestInterface::class
        )->setParam(
            'rid',
            $roleAdmin->getId()
        );

        $this->_block = Bootstrap::getObjectManager()->create(
            Edit::class
        );
    }
}
