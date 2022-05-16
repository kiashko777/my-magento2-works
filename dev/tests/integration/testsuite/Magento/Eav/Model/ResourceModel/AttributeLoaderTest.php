<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Eav\Model\ResourceModel;

use Magento\Eav\Model\Entity\Type;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;
use ReflectionObject;

/**
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 * @magentoDataFixture Magento/Eav/_files/attribute_for_search.php
 */
class AttributeLoaderTest extends TestCase
{
    /**
     * @var AttributeLoader
     */
    private $attributeLoader;

    /**
     * @param string[] $expectedAttributeCodes
     * @param int|null $attributeSetId
     * @dataProvider getAttributesDataProvider
     */
    public function testGetAttributes($expectedAttributeCodes, $attributeSetId = null)
    {
        $attributes = $this->attributeLoader->getAttributes('Test\Entity\Type', $attributeSetId);
        $this->assertEquals(count($expectedAttributeCodes), count($attributes));
        $attributeCodes = [];
        foreach ($attributes as $attribute) {
            $attributeCodes[] = $attribute->getAttributeCode();
        }
        $this->assertEquals($expectedAttributeCodes, $attributeCodes);
        $attributes2 = $this->attributeLoader->getAttributes('Test\Entity\Type', $attributeSetId);
        $this->assertEquals($attributes, $attributes2);
    }

    public function getAttributesDataProvider()
    {
        $objectManager = Bootstrap::getObjectManager();
        $entityType = $objectManager->create(Type::class)
            ->loadByCode('order');
        $attributeSetId = $entityType->getDefaultAttributeSetId();

        return [
            'all' => [
                [
                    'attribute_for_search_1',
                    'attribute_for_search_2',
                    'attribute_for_search_3',
                ]
            ],
            "$attributeSetId" => [
                [
                    'attribute_for_search_1',
                    'attribute_for_search_2',
                ],
                $attributeSetId,
            ]
        ];
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $metadataPool = $objectManager->create(
            MetadataPool::class,
            [
                'metadata' => [
                    'Test\Entity\Type' => [
                        'entityTableName' => 'test_entity',
                        'eavEntityType' => 'test',
                        'identifierField' => 'entity_id',
                    ]
                ]
            ]
        );
        $this->attributeLoader = $objectManager->create(AttributeLoader::class, ['metadataPool' => $metadataPool]);
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
