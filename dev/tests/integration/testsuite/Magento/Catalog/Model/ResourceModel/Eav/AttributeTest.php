<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\ResourceModel\Eav;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Entity;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Catalog\Model\ResourceModel\Eav\Attribute.
 */
class AttributeTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Attribute
     */
    private $model;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var int|string
     */
    private $catalogProductEntityType;

    /**
     * Test Create -> Read -> Update -> Delete attribute operations.
     *
     * @return void
     */
    public function testCRUD()
    {
        $this->model->setAttributeCode('test')
            ->setEntityTypeId($this->catalogProductEntityType)
            ->setFrontendLabel('test')
            ->setIsUserDefined(1);
        $crud = new Entity($this->model, [AttributeInterface::FRONTEND_LABEL => uniqid()]);
        $crud->testCrud();
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_attribute.php
     *
     * @return void
     */
    public function testAttributeSaveWithChangedEntityType(): void
    {
        $this->expectException(
            LocalizedException::class
        );
        $this->expectExceptionMessage('Do not change entity type.');

        $attribute = $this->attributeRepository->get($this->catalogProductEntityType, 'test_attribute_code_333');
        $attribute->setEntityTypeId(1);
        $attribute->save();
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->get(Attribute::class);
        $this->attributeRepository = $this->objectManager->get(AttributeRepositoryInterface::class);
        $this->catalogProductEntityType = $this->objectManager->get(Config::class)
            ->getEntityType('catalog_product')
            ->getId();
    }
}
