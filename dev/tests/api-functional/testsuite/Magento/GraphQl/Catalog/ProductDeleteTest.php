<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Catalog;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Registry;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Test that product is not present in GQL after it was deleted
 */
class ProductDeleteTest extends GraphQlAbstract
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Catalog/_files/product_simple_with_all_fields.php
     */
    public function testQuerySimpleProductAfterDelete()
    {
        $productSku = 'simple';

        $query = <<<QUERY
{
    products(filter: {sku: {eq: "{$productSku}"}})
    {
        items {
            id
        }
    }
}
QUERY;
        // get customer ID token
        /** @var CustomerTokenServiceInterface $customerTokenService */
        $customerTokenService = $this->objectManager->create(
            CustomerTokenServiceInterface::class
        );
        $customerToken = $customerTokenService->createCustomerAccessToken('customer@example.com', 'password');

        $headerMap = ['Authorization' => 'Bearer ' . $customerToken];
        $response = $this->graphQlQuery($query, [], '', $headerMap);
        $this->assertArrayHasKey('products', $response);
        $this->assertArrayHasKey('items', $response['products']);
        $this->assertCount(1, $response['products']['items']);

        // Delete the product and verify it is actually not accessible via the storefront anymore
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = ObjectManager::getInstance()->get(ProductRepositoryInterface::class);

        $registry = ObjectManager::getInstance()->get(Registry::class);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);
        $productRepository->deleteById($productSku);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);

        $response = $this->graphQlQuery($query, [], '', $headerMap);
        $this->assertArrayHasKey('products', $response);
        $this->assertArrayHasKey('items', $response['products']);
        $this->assertCount(0, $response['products']['items']);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
