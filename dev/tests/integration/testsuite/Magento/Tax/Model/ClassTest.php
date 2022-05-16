<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Registry;
use Magento\Tax\Model\Calculation\Rule;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class ClassTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @magentoDbIsolation enabled
     */
    public function testCheckClassCanBeDeletedCustomerClassAssertException()
    {
        /** @var $model ClassModel */
        $model = $this->_objectManager->create(
            ClassModel::class
        )->getCollection()->setClassTypeFilter(
            ClassModel::TAX_CLASS_TYPE_CUSTOMER
        )->getFirstItem();

        $this->expectException(CouldNotDeleteException::class);
        $model->delete();
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCheckClassCanBeDeletedProductClassAssertException()
    {
        /** @var $model ClassModel */
        $model = $this->_objectManager->create(
            ClassModel::class
        )->getCollection()->setClassTypeFilter(
            ClassModel::TAX_CLASS_TYPE_PRODUCT
        )->getFirstItem();

        $this->_objectManager->create(
            Product::class
        )->setTypeId(
            'simple'
        )->setAttributeSetId(
            4
        )->setName(
            'Simple Products'
        )->setSku(
            uniqid()
        )->setPrice(
            10
        )->setMetaTitle(
            'meta title'
        )->setMetaKeyword(
            'meta keyword'
        )->setMetaDescription(
            'meta description'
        )->setVisibility(
            Visibility::VISIBILITY_BOTH
        )->setStatus(
            Status::STATUS_ENABLED
        )->setTaxClassId(
            $model->getId()
        )->save();

        $this->expectException(CouldNotDeleteException::class);
        $model->delete();
    }

    /**
     * @magentoDbIsolation enabled
     * @dataProvider classesDataProvider
     */
    public function testCheckClassCanBeDeletedPositiveResult($classType)
    {
        /** @var $model ClassModel */
        $model = $this->_objectManager->create(ClassModel::class);
        $model->setClassName('TaxClass' . uniqid())->setClassType($classType)->isObjectNew(true);
        $model->save();

        $model->delete();
    }

    public function classesDataProvider()
    {
        return [
            [ClassModel::TAX_CLASS_TYPE_CUSTOMER],
            [ClassModel::TAX_CLASS_TYPE_PRODUCT]
        ];
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testCheckClassCanBeDeletedCustomerClassUsedInTaxRule()
    {
        /** @var $registry Registry */
        $registry = $this->_objectManager->get(Registry::class);
        /** @var $taxRule Rule */
        $taxRule = $registry->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $customerClasses = $taxRule->getCustomerTaxClasses();

        /** @var $model ClassModel */
        $model = $this->_objectManager->create(ClassModel::class)->load($customerClasses[0]);
        $this->expectException(CouldNotDeleteException::class);
        $this->expectExceptionMessage('You cannot delete this tax class because it is used in' .
            ' Tax Rules. You have to delete the rules it is used in first.');
        $model->delete();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testCheckClassCanBeDeletedProductClassUsedInTaxRule()
    {
        /** @var $registry Registry */
        $registry = $this->_objectManager->get(Registry::class);
        /** @var $taxRule Rule */
        $taxRule = $registry->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $productClasses = $taxRule->getProductTaxClasses();

        /** @var $model ClassModel */
        $model = $this->_objectManager->create(ClassModel::class)->load($productClasses[0]);
        $this->expectException(CouldNotDeleteException::class);
        $this->expectExceptionMessage('You cannot delete this tax class because it is used in' .
            ' Tax Rules. You have to delete the rules it is used in first.');
        $model->delete();
    }

    protected function setUp(): void
    {
        /** @var $objectManager ObjectManager */
        $this->_objectManager = Bootstrap::getObjectManager();
    }
}
