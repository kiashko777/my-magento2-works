<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Eav\Model;

use Magento\Catalog\Api\Data\CategoryAttributeInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AttributeManagementTest extends TestCase
{
    /**
     * @var AttributeManagementInterface
     */
    private $model;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Verify that collection in service used correctly
     */
    public function testGetList()
    {
        $productAttributeSetId = $this->getAttributeSetId(
            ProductAttributeInterface::ENTITY_TYPE_CODE
        );
        $productAttributes = $this->model->getAttributes(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            $productAttributeSetId
        );
        // Verify that result contains only product attributes
        $this->verifyAttributeSetIds($productAttributes, $productAttributeSetId);

        $categoryAttributeSetId = $this->getAttributeSetId(
            CategoryAttributeInterface::ENTITY_TYPE_CODE
        );
        $categoryAttributes = $this->model->getAttributes(
            CategoryAttributeInterface::ENTITY_TYPE_CODE,
            $categoryAttributeSetId
        );
        // Verify that result contains only category attributes
        $this->verifyAttributeSetIds($categoryAttributes, $categoryAttributeSetId);
    }

    /**
     * @param string $entityTypeCode
     * @return int
     */
    private function getAttributeSetId($entityTypeCode)
    {
        /** @var Config $eavConfig */
        $eavConfig = $this->objectManager->create(Config::class);
        return $eavConfig->getEntityType($entityTypeCode)->getDefaultAttributeSetId();
    }

    /**
     * @param array $items
     * @param string $attributeSetId
     * @return void
     */
    private function verifyAttributeSetIds(array $items, $attributeSetId)
    {
        /** @var AbstractAttribute $item */
        foreach ($items as $item) {
            $this->assertEquals($attributeSetId, $item->getAttributeSetId());
        }
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(AttributeManagementInterface::class);
    }
}
