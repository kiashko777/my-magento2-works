<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Eav\Model\Entity;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

/**
 * @magentoAppIsolation enabled
 * @magentoDataFixture Magento/Eav/_files/attribute_for_search.php
 */
class AttributeLoaderTest extends TestCase
{
    /**
     * @var AttributeLoader
     */
    private $attributeLoader;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var AbstractEntity
     */
    private $resource;

    /**
     * @param int $expectedNumOfAttributesByCode
     * @param int $expectedNumOfAttributesByTable
     * @param DataObject|null $object
     * @dataProvider loadAllAttributesDataProvider
     */
    public function testLoadAllAttributesTheFirstTime(
        $expectedNumOfAttributesByCode,
        $expectedNumOfAttributesByTable,
        $object
    )
    {
        // Before load all attributes
        $attributesByCode = $this->resource->getAttributesByCode();
        $attributesByTable = $this->resource->getAttributesByTable();
        $this->assertCount(0, $attributesByCode);
        $this->assertCount(0, $attributesByTable);

        // Load all attributes
        $resource2 = $this->attributeLoader->loadAllAttributes(
            $this->resource,
            $object
        );
        $attributesByCode2 = $resource2->getAttributesByCode();
        $attributesByTable2 = $resource2->getAttributesByTable();
        $this->assertEquals($expectedNumOfAttributesByCode, count($attributesByCode2));
        $this->assertEquals($expectedNumOfAttributesByTable, count($attributesByTable2));
    }

    public function loadAllAttributesDataProvider()
    {
        /** @var Type $entityType */
        $entityType = Bootstrap::getObjectManager()->create(Type::class)
            ->loadByCode('order');
        $attributeSetId = $entityType->getDefaultAttributeSetId();
        return [
            [
                13,
                2,
                null
            ],
            [
                10,
                1,
                new DataObject(
                    [
                        'attribute_set_id' => $attributeSetId,
                        'store_id' => 0
                    ]
                ),
            ],
            [
                10,
                1,
                new DataObject(
                    [
                        'attribute_set_id' => $attributeSetId,
                        'store_id' => 10
                    ]
                ),
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->attributeLoader = $this->objectManager->get(AttributeLoader::class);
        $entityType = $this->objectManager->create(Type::class)
            ->loadByCode('test');
        $context = $this->objectManager->get(Context::class);
        $this->resource = $this->getMockBuilder(AbstractEntity::class)
            ->setConstructorArgs([$context])
            ->setMethods(['getEntityType', 'getLinkField'])
            ->getMock();
        $this->resource->method('getEntityType')
            ->willReturn($entityType);
        $this->resource->method('getLinkField')
            ->willReturn('link_field');
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $reflection = new ReflectionObject($this);
        foreach ($reflection->getProperties() as $property) {
            if (!$property->isStatic() && 0 !== strpos($property->getDeclaringClass()->getName(), 'PHPUnit')) {
                $property->setAccessible(true);
                $property->setValue($this, null);
            }
        }
    }
}
