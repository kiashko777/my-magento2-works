<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Weee\Model\ResourceModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Attribute;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Eav\Model\Entity\Attribute as EavAttribute;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Magento\Catalog\Model\ResourceModel\Attribute class
 * with backend model Magento\Weee\Model\Attribute\Backend\Weee\Tax.
 *
 * @see Magento\Catalog\Model\ResourceModel\Attribute
 */
class AttributeTest extends TestCase
{
    /**
     * @var Product
     */
    protected $productResource;
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var Attribute
     */
    private $model;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * Test to delete entity attribute with type "Fixed Products Tax".
     *
     * @magentoDataFixture Magento/Weee/_files/fixed_product_attribute.php
     * @return void
     */
    public function testDeleteEntityFixedTax(): void
    {
        /* @var EavAttribute $attribute */
        $attribute = $this->objectManager->get(EavAttribute::class);
        $attribute->loadByCode(\Magento\Catalog\Model\Product::ENTITY, 'fixed_product_attribute');

        $entityEavAttributeRow = $this->getEavEntityAttributeRow(
            (int)$attribute->getEntityTypeId(),
            4,
            (int)$attribute->getId()
        );
        $this->assertNotEmpty(
            $entityEavAttributeRow['entity_attribute_id'],
            'The record is absent in table `eav_entity_attribute` for `fixed_product_attribute`'
        );

        $attribute->setData('entity_attribute_id', $entityEavAttributeRow['entity_attribute_id']);
        $this->model->deleteEntity($attribute);

        $entityEavAttributeRow = $this->getEavEntityAttributeRow(
            (int)$attribute->getEntityTypeId(),
            4,
            (int)$attribute->getId()
        );
        $this->assertEmpty(
            $entityEavAttributeRow,
            'The record was not removed from table `eav_entity_attribute` for `fixed_product_attribute`'
        );
    }

    /**
     * Retrieve eav attribute row.
     *
     * @param int $entityTypeId
     * @param int $attributeSetId
     * @param int $attributeId
     * @return array|false
     */
    private function getEavEntityAttributeRow(int $entityTypeId, int $attributeSetId, int $attributeId)
    {
        $connection = $this->productResource->getConnection();
        $select = $connection->select()
            ->from($this->productResource->getTable('eav_entity_attribute'))
            ->where('attribute_set_id=?', $attributeSetId)
            ->where('attribute_id=?', $attributeId)
            ->where('entity_type_id=?', $entityTypeId);

        return $connection->fetchRow($select);
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->get(
            Attribute::class
        );
        $this->productResource = $this->objectManager->get(
            Product::class
        );
        $this->productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $this->metadataPool = $this->objectManager->get(MetadataPool::class);
    }
}
