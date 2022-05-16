<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model\TaxClass;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Tax\Api\Data\TaxClassInterface;
use Magento\Tax\Api\Data\TaxClassInterfaceFactory;
use Magento\Tax\Api\Data\TaxClassSearchResultsInterface;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\Tax\Model\ClassModel as TaxClassModel;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    const SAMPLE_TAX_CLASS_NAME = 'Wholesale Customer';
    /**
     * @var Repository
     */
    private $taxClassRepository;
    /**
     * @var TaxClassInterfaceFactory
     */
    private $taxClassFactory;
    /**
     * @var TaxClassModel
     */
    private $taxClassModel;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var array
     */
    private $predefinedTaxClasses;

    /**
     * @magentoDbIsolation enabled
     */
    public function testSave()
    {
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName(self::SAMPLE_TAX_CLASS_NAME)
            ->setClassType(TaxClassManagementInterface::TYPE_CUSTOMER);
        $taxClassId = $this->taxClassRepository->save($taxClassDataObject);
        $this->assertEquals(self::SAMPLE_TAX_CLASS_NAME, $this->taxClassModel->load($taxClassId)->getClassName());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveThrowsExceptionIfGivenTaxClassNameIsNotUnique()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('A class with the same name already exists for ClassType PRODUCT.');

        //ClassType and name combination has to be unique.
        //Testing against existing Tax classes which are already setup when the instance is installed
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName($this->predefinedTaxClasses[TaxClassModel::TAX_CLASS_TYPE_PRODUCT])
            ->setClassType(TaxClassManagementInterface::TYPE_PRODUCT);
        $this->taxClassRepository->save($taxClassDataObject);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveThrowsExceptionIfGivenDataIsInvalid()
    {
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName(null)
            ->setClassType('');
        try {
            $this->taxClassRepository->save($taxClassDataObject);
        } catch (InputException $e) {
            $errors = $e->getErrors();
            $this->assertEquals('"class_name" is required. Enter and try again.', $errors[0]->getMessage());
            $this->assertEquals('"class_type" is required. Enter and try again.', $errors[1]->getMessage());
        }
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testGet()
    {
        $taxClassName = 'Get Me';
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName($taxClassName)
            ->setClassType(TaxClassManagementInterface::TYPE_CUSTOMER);
        $taxClassId = $this->taxClassRepository->save($taxClassDataObject);
        $data = $this->taxClassRepository->get($taxClassId);
        $this->assertEquals($taxClassId, $data->getClassId());
        $this->assertEquals($taxClassName, $data->getClassName());
        $this->assertEquals(TaxClassManagementInterface::TYPE_CUSTOMER, $data->getClassType());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testGetList()
    {
        $taxClassName = 'Get Me';
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName($taxClassName)
            ->setClassType(TaxClassManagementInterface::TYPE_CUSTOMER);
        $taxClassId = $this->taxClassRepository->save($taxClassDataObject);
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(
            SearchCriteriaBuilder::class
        );
        /** @var TaxClassSearchResultsInterface */
        $searchResult = $this->taxClassRepository->getList($searchCriteriaBuilder->create());
        $items = $searchResult->getItems();
        /** @var TaxClassInterface */
        $taxClass = array_pop($items);
        $this->assertInstanceOf(TaxClassInterface::class, $taxClass);
        $this->assertEquals($taxClassName, $taxClass->getClassName());
        $this->assertEquals($taxClassId, $taxClass->getClassId());
        $this->assertEquals(TaxClassManagementInterface::TYPE_CUSTOMER, $taxClass->getClassType());
    }

    /**
     */
    public function testGetThrowsExceptionIfRequestedTaxClassDoesNotExist()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No such entity with class_id = -9999');

        $this->taxClassRepository->get(-9999);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testDeleteById()
    {
        $taxClassName = 'Delete Me';
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName($taxClassName)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_CUSTOMER);
        $taxClassId = $this->taxClassRepository->save($taxClassDataObject);

        $this->assertTrue($this->taxClassRepository->deleteById($taxClassId));

        // Verify if the tax class is deleted
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage("No such entity with class_id = $taxClassId");
        $this->taxClassRepository->deleteById($taxClassId);
    }

    /**
     */
    public function testDeleteByIdThrowsExceptionIfTargetTaxClassDoesNotExist()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('No such entity with class_id = 99999');

        $nonexistentTaxClassId = 99999;
        $this->taxClassRepository->deleteById($nonexistentTaxClassId);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveWithExistingTaxClass()
    {
        $taxClassName = 'New Class Name';
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName($taxClassName)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_CUSTOMER);
        $taxClassId = $this->taxClassRepository->save($taxClassDataObject);
        $this->assertEquals($taxClassName, $this->taxClassModel->load($taxClassId)->getClassName());

        $updatedTaxClassName = 'Updated Class Name';
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName($updatedTaxClassName)
            ->setClassId($taxClassId)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_CUSTOMER);

        $this->assertEquals($taxClassId, $this->taxClassRepository->save($taxClassDataObject));

        $this->assertEquals($updatedTaxClassName, $this->taxClassModel->load($taxClassId)->getClassName());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveThrowsExceptionIfTargetTaxClassHasDifferentClassType()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Updating classType is not allowed.');

        $taxClassName = 'New Class Name';
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName($taxClassName)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_CUSTOMER);
        $taxClassId = $this->taxClassRepository->save($taxClassDataObject);
        $this->assertEquals($taxClassName, $this->taxClassModel->load($taxClassId)->getClassName());

        $updatedTaxClassName = 'Updated Class Name';
        $taxClassDataObject = $this->taxClassFactory->create();
        $taxClassDataObject->setClassName($updatedTaxClassName)
            ->setClassId($taxClassId)
            ->setClassType(TaxClassModel::TAX_CLASS_TYPE_PRODUCT);

        $this->taxClassRepository->save($taxClassDataObject);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->taxClassRepository = $this->objectManager->create(TaxClassRepositoryInterface::class);
        $this->taxClassFactory = $this->objectManager->create(TaxClassInterfaceFactory::class);
        $this->taxClassModel = $this->objectManager->create(TaxClassModel::class);
        $this->predefinedTaxClasses = [
            TaxClassManagementInterface::TYPE_PRODUCT => 'Taxable Goods',
            TaxClassManagementInterface::TYPE_CUSTOMER => 'Retail Customer',
        ];
    }
}
