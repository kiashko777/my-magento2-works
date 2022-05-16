<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\Product\Attribute\Save;

/**
 * @magentoDbIsolation enabled
 * @magentoDataFixture  Magento/Catalog/_files/product_text_attribute.php
 * @magentoDataFixture Magento/Catalog/_files/second_product_simple.php
 */
class AttributeTextAreaTest extends AbstractAttributeTest
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_text_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/second_product_simple.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple_out_of_stock.php
     * @dataProvider uniqueAttributeValueProvider
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
     * @inheritdoc
     */
    public function testUniqueAttribute(string $firstSku, string $secondSku): void
    {
        parent::testUniqueAttribute($firstSku, $secondSku);
    }

    /**
     * @inheritdoc
     */
    public function productProvider(): array
    {
        return [
            [
                'product_sku' => 'simple2',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function uniqueAttributeValueProvider(): array
    {
        return [
            [
                'first_product_sku' => 'simple2',
                'second_product_sku' => 'simple-out-of-stock',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getAttributeCode(): string
    {
        return 'text_attribute';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAttributeValue(): string
    {
        return 'default text area value';
    }
}
