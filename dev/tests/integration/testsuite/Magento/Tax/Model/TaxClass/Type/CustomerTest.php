<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model\TaxClass\Type;

use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Tax\Model\ClassModel;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    const GROUP_CODE = 'Test Group';
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @magentoDbIsolation enabled
     */
    public function testIsAssignedToObjects()
    {
        /** @var $objectManager ObjectManager */
        $this->_objectManager = Bootstrap::getObjectManager();
        $groupFactory = $this->_objectManager->create(GroupInterfaceFactory::class);

        /* Create a tax class */
        $model = $this->_objectManager->create(ClassModel::class);
        $model->setClassName("Test Group Tax Class")
            ->setClassType(ClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->isObjectNew(true);
        $model->save();
        $taxClassId = $model->getId();

        $model->setId($taxClassId);
        /** @var $groupRepository GroupRepositoryInterface */
        $groupRepository = $this->_objectManager->create(GroupRepositoryInterface::class);
        $group = $groupFactory->create()->setId(null)->setCode(self::GROUP_CODE)->setTaxClassId($taxClassId);
        $groupRepository->save($group);

        /** @var $model Customer */
        $model = $this->_objectManager->create(Customer::class);
        $model->setId($taxClassId);
        $this->assertTrue($model->isAssignedToObjects());
    }
}
