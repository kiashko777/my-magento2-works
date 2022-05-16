<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Eav\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

/**
 * Test class for Magento\Eav\Setup\EavSetup.
 * @magentoDbIsolation enabled
 */
class EavSetupTest extends TestCase
{
    /**
     * Eav setup.
     *
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * Verify that add attribute work correct attribute_code.
     *
     * @param string $attributeCode
     *
     * @dataProvider addAttributeDataProvider
     */
    public function testAddAttribute($attributeCode)
    {
        $attributeData = $this->getAttributeData();

        $this->eavSetup->addAttribute(Product::ENTITY, $attributeCode, $attributeData);

        $attribute = $this->eavSetup->getAttribute(Product::ENTITY, $attributeCode);

        $this->assertEmpty(array_diff($attributeData, $attribute));
    }

    /**
     * Get simple attribute data.
     */
    private function getAttributeData()
    {
        $attributeData = [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Eav Setup Test',
            'input' => 'text',
            'class' => '',
            'source' => '',
            'global' => Attribute::SCOPE_STORE,
            'visible' => 0,
            'required' => 0,
            'user_defined' => 1,
            'default' => 'none',
            'searchable' => 0,
            'filterable' => 0,
            'comparable' => 0,
            'visible_on_front' => 0,
            'unique' => 0,
            'apply_to' => 'category',
        ];

        return $attributeData;
    }

    /**
     * Data provider for testAddAttributeThrowException().
     *
     * @return array
     */
    public function addAttributeDataProvider()
    {
        return [
            ['eav_setup_test'],
            ['characters_59_characters_59_characters_59_characters_59_59_'],
        ];
    }

    /**
     * Verify that add attribute throw exception if attribute_code is not valid.
     *
     * @param string|null $attributeCode
     *
     * @dataProvider addAttributeThrowExceptionDataProvider
     */
    public function testAddAttributeThrowException($attributeCode)
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('An attribute code must not be less than 1 and more than 60 characters.');

        $attributeData = $this->getAttributeData();

        $this->eavSetup->addAttribute(Product::ENTITY, $attributeCode, $attributeData);
    }

    /**
     * Data provider for testAddAttributeThrowException().
     *
     * @return array
     */
    public function addAttributeThrowExceptionDataProvider()
    {
        return [
            [null],
            [''],
            [' '],
            ['more_than_60_characters_more_than_more_than_60_characters_more'],
        ];
    }

    /**
     * Verify that add attribute throw exception if attribute_code is not valid.
     *
     * @param string|null $attributeCode
     *
     * @dataProvider addInvalidAttributeThrowExceptionDataProvider
     */
    public function testAddInvalidAttributeThrowException($attributeCode)
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in this field,');

        $attributeData = $this->getAttributeData();
        $this->eavSetup->addAttribute(Product::ENTITY, $attributeCode, $attributeData);
    }

    /**
     * Data provider for testAddInvalidAttributeThrowException().
     *
     * @return array
     */
    public function addInvalidAttributeThrowExceptionDataProvider()
    {
        return [
            ['1first_character_is_not_letter'],
            ['attribute.with.dots'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->eavSetup = $objectManager->create(EavSetup::class);
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
