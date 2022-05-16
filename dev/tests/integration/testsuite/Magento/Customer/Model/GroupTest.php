<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model;

use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\TestFramework\Entity;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{
    /**
     * @var Group
     */
    protected $groupModel;

    /**
     * @var GroupInterfaceFactory
     */
    protected $groupFactory;

    public function testCRUD()
    {
        $this->groupModel->setCode('test');
        $crud = new Entity($this->groupModel, ['customer_group_code' => uniqid()]);
        $crud->testCrud();
    }

    protected function setUp(): void
    {
        $this->groupModel = Bootstrap::getObjectManager()->create(
            Group::class
        );
        $this->groupFactory = Bootstrap::getObjectManager()->create(
            GroupInterfaceFactory::class
        );
    }
}
