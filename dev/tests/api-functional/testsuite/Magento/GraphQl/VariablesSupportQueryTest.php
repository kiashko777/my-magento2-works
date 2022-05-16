<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQl;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class VariablesSupportQueryTest extends GraphQlAbstract
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @magentoApiDataFixture Magento/Catalog/_files/products_list.php
     */
    public function testQueryObjectVariablesSupport()
    {
        $productSku = 'simple-249';
        $minPrice = 153;

        $query
            = <<<'QUERY'
query GetProductsQuery($pageSize: Int, $filterInput: ProductAttributeFilterInput, $priceSort: SortEnum) {
  products(
    pageSize: $pageSize
    filter: $filterInput
    sort: {price: $priceSort}
  ) {
    items {
      sku
      price {
        minimalPrice {
          amount {
            value
            currency
          }
        }
      }
    }
  }
}
QUERY;

        $variables = [
            'pageSize' => 1,
            'priceSort' => 'ASC',
            'filterInput' => [
                'price' => [
                    'from' => 150,
                ],
            ],
        ];

        $response = $this->graphQlQuery($query, $variables);
        /** @var Product $product */
        $product = $this->productRepository->get($productSku, false, null, true);

        self::assertArrayHasKey('products', $response);
        self::assertArrayHasKey('items', $response['products']);
        self::assertCount(1, $response['products']['items']);
        self::assertArrayHasKey(0, $response['products']['items']);
        self::assertEquals($product->getSku(), $response['products']['items'][0]['sku']);
        self::assertEquals(
            $minPrice,
            $response['products']['items'][0]['price']['minimalPrice']['amount']['value']
        );
    }

    protected function setUp(): void
    {
        $this->productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
    }
}
