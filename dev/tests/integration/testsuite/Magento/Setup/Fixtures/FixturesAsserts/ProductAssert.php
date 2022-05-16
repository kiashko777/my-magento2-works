<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures\FixturesAsserts;

use AssertionError;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\DB\Sql\ColumnValueExpressionFactory;

/**
 * Class ProductAssert
 *
 * Class provides assertions about products count and products type
 * that helps validate them after running setup:performance:generate-fixtures command
 */
class ProductAssert
{
    /**
     * @var
     */
    protected $productCollectionFactory;

    /**
     * @var ColumnValueExpressionFactory
     */
    protected $expressionFactory;

    /**
     * @param CollectionFactory $productCollectionFactory
     * @param ColumnValueExpressionFactory $expressionFactory
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        ColumnValueExpressionFactory         $expressionFactory
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * Performs assertion on generated products count
     * Accepts products sku pattern to calculate count and theirs expected count
     *
     * @param string $skuPattern
     * @param int $expectedCount
     * @return void
     * @throws AssertionError
     */
    public function assertProductsCount($skuPattern, $expectedCount)
    {
        $productSkuPattern = str_replace('%s', '[0-9]+', $skuPattern);
        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->getSelect()
            ->where('sku ?', $this->expressionFactory->create([
                'expression' => 'REGEXP \'^' . $productSkuPattern . '$\''
            ]));

        if ($expectedCount !== count($productCollection)) {
            throw new AssertionError(
                sprintf(
                    'Expected amount of products with sku pattern "%s" not equals actual amount',
                    $skuPattern
                )
            );
        }
    }

    /**
     * Performs assertion that product has expected product type
     *
     * @param string $expectedProductType
     * @param ProductInterface $product
     * @throws AssertionError
     */
    public function assertProductType($expectedProductType, $product)
    {
        if ($expectedProductType !== $product->getTypeId()) {
            throw new AssertionError('Products type is wrong');
        }
    }
}
