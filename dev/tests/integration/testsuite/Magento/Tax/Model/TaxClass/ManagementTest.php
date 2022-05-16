<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model\TaxClass;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Tax\Api\Data\TaxClassInterfaceFactory;
use Magento\Tax\Api\Data\TaxClassKeyInterface;
use Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ManagementTest extends TestCase
{
    /**
     * @var Repository
     */
    private $taxClassRepository;

    /**
     * @var Management
     */
    private $taxClassManagement;

    /**
     * @var TaxClassInterfaceFactory
     */
    private $taxClassFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @magentoDbIsolation enabled
     */
    public function testGetTaxClassId()
    {
        $taxClassName = 'Get Me';
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName($taxClassName)
            ->setClassType(TaxClassManagementInterface::TYPE_CUSTOMER);

        $taxClassId = $this->taxClassRepository->save($taxClassDataObject);
        /** @var TaxClassKeyInterfaceFactory $taxClassKeyFactory */
        $taxClassKeyFactory = $this->objectManager->create(TaxClassKeyInterfaceFactory::class);
        $taxClassKeyTypeId = $taxClassKeyFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $taxClassKeyTypeId,
            [
                Key::KEY_TYPE => TaxClassKeyInterface::TYPE_ID,
                Key::KEY_VALUE => $taxClassId,
            ],
            TaxClassKeyInterface::class
        );
        $this->assertEquals(
            $taxClassId,
            $this->taxClassManagement->getTaxClassId($taxClassKeyTypeId, TaxClassManagementInterface::TYPE_CUSTOMER)
        );
        $taxClassKeyTypeName = $taxClassKeyFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $taxClassKeyTypeName,
            [
                Key::KEY_TYPE => TaxClassKeyInterface::TYPE_NAME,
                Key::KEY_VALUE => $taxClassName,
            ],
            TaxClassKeyInterface::class
        );
        $this->assertEquals(
            $taxClassId,
            $this->taxClassManagement->getTaxClassId($taxClassKeyTypeId, TaxClassManagementInterface::TYPE_CUSTOMER)
        );
        $this->assertNull($this->taxClassManagement->getTaxClassId(null));
        $this->assertNull(
            $this->taxClassManagement->getTaxClassId($taxClassKeyTypeName, TaxClassManagementInterface::TYPE_PRODUCT)
        );
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->taxClassRepository = $this->objectManager->create(TaxClassRepositoryInterface::class);
        $this->taxClassManagement = $this->objectManager->create(TaxClassManagementInterface::class);
        $this->taxClassFactory = $this->objectManager->create(TaxClassInterfaceFactory::class);
        $this->dataObjectHelper = $this->objectManager->create(DataObjectHelper::class);
    }
}
