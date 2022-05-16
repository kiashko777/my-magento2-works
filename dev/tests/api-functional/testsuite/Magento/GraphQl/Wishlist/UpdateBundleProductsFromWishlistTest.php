<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Wishlist;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Wishlist\Model\WishlistFactory;

/**
 * Test coverage for updating a bundle product from wishlist
 */
class UpdateBundleProductsFromWishlistTest extends GraphQlAbstract
{
    /**
     * @var CustomerTokenServiceInterface
     */
    private $customerTokenService;

    /**
     * @var WishlistFactory
     */
    private $wishlistFactory;

    /**
     * @var mixed
     */
    private $productRepository;

    /**
     * Test that a wishlist item bundle product is properly updated.
     *
     * This includes the selected options for the bundle product.
     *
     * @magentoConfigFixture default_store wishlist/general/active 1
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Bundle/_files/bundle_product_dropdown_options.php
     * @throws Exception
     */
    public function testUpdateBundleProductWithOptions(): void
    {
        // Add the fixture bundle product to the fixture customer's wishlist
        $wishlist = $this->addProductToWishlist();
        $wishlistId = (int)$wishlist['addProductsToWishlist']['wishlist']['id'];
        $wishlistItemId = (int)$wishlist['addProductsToWishlist']['wishlist']['items_v2']['items'][0]['id'];
        $previousItemsCount = $wishlist['addProductsToWishlist']['wishlist']['items_count'];

        // Set the new values to update the wishlist item with
        $newQuantity = 5;
        $newDescription = 'This is a test.';
        $bundleProductOptions = $this->getBundleProductOptions('bundle-product-dropdown-options');
        $newBundleOptionUid = $bundleProductOptions[1]["uid"];

        // Update the newly added wishlist item as the fixture customer
        $query = $this->getUpdateQuery(
            $wishlistItemId,
            $newQuantity,
            $newDescription,
            $newBundleOptionUid,
            $wishlistId
        );
        $response = $this->graphQlMutation($query, [], '', $this->getHeaderMap());

        // Assert that the response has the expected base properties
        self::assertArrayHasKey('updateProductsInWishlist', $response);
        self::assertArrayHasKey('wishlist', $response['updateProductsInWishlist']);

        // Assert that the wishlist item count is unchanged
        $responseWishlist = $response['updateProductsInWishlist']['wishlist'];
        self::assertEquals($previousItemsCount, $responseWishlist['items_count']);

        // Assert that the wishlist item quantity and description are updated
        $responseWishlistItem = $responseWishlist['items_v2']['items'][0];
        self::assertEquals($newQuantity, $responseWishlistItem['quantity']);
        self::assertEquals($newDescription, $responseWishlistItem['description']);

        // Assert that the bundle option for this wishlist item is accurate
        self::assertNotEmpty($responseWishlistItem['bundle_options']);
        $responseBundleOption = $responseWishlistItem['bundle_options'][0];
        self::assertEquals('Dropdown Options', $responseBundleOption['label']);
        self::assertEquals(Select::NAME, $responseBundleOption['type']);

        // Assert that the selected value for this bundle option is updated
        self::assertNotEmpty($responseBundleOption['values']);
        $responseOptionSelection = $responseBundleOption['values'][0];
        self::assertEquals($newBundleOptionUid, $responseOptionSelection['uid']);
        self::assertEquals('Simple Product2', $responseOptionSelection['label']);
        self::assertEquals(1, $responseOptionSelection['quantity']);
        self::assertEquals(10, $responseOptionSelection['price']);
    }

    /**
     * Add a product to the to the wishlist.
     *
     * @return array
     * @throws AuthenticationException
     */
    private function addProductToWishlist(): array
    {
        $bundleProductSku = 'bundle-product-dropdown-options';
        $bundleProductOptions = $this->getBundleProductOptions($bundleProductSku);
        $initialBundleOptionUid = $bundleProductOptions[0]["uid"];
        $initialQuantity = 2;

        $query = $this->getAddQuery($bundleProductSku, $initialQuantity, $initialBundleOptionUid);
        return $this->graphQlMutation($query, [], '', $this->getHeaderMap());
    }

    /**
     * Get the available options for the specified bundle product.
     *
     * @param string $bundleProductSku
     * @return array
     */
    private function getBundleProductOptions(string $bundleProductSku)
    {
        $query = $this->getBundleProductSearchQuery($bundleProductSku);
        $response = $this->graphQlQuery($query);

        $bundleProduct = $response["products"]["items"][0];
        $bundleProductOptions = $bundleProduct["items"][0]["options"];

        return $bundleProductOptions;
    }

    /**
     * Returns the GraphQl product search query for a bundle product.
     *
     * @param string $bundleProductSku
     * @return string
     */
    private function getBundleProductSearchQuery(string $bundleProductSku): string
    {
        return <<<QUERY
query {
  products(search: "{$bundleProductSku}"){
    items {
      uid
      sku
      name
      ... on BundleProduct {
        items {
          uid
          title
          type
          options {
            id
            uid
          }
        }
      }
    }
  }
}
QUERY;
    }

    /**
     * Returns the GraphQl mutation for adding an item to the wishlist.
     *
     * @param string $sku
     * @param int $qty
     * @param string $bundleOptions
     * @param int $wishlistId
     * @return string
     */
    private function getAddQuery(
        string $sku,
        int    $qty,
        string $bundleOptions,
        int    $wishlistId = 0
    ): string
    {
        return <<<MUTATION
mutation {
  addProductsToWishlist(
    wishlistId: {$wishlistId},
    wishlistItems: [
      {
        sku: "{$sku}"
        quantity: {$qty}
        selected_options: [
          "{$bundleOptions}"
        ]
      }
    ]
) {
    user_errors {
      code
      message
    }
    wishlist {
      id
      sharing_code
      items_count
      updated_at
      items_v2 {
        items{
          id
          quantity
          added_at
          ... on BundleWishlistItem {
            bundle_options {
              id
              label
              type
              values {
                id
                label
                quantity
                price
              }
            }
          }
        }
      }
    }
  }
}
MUTATION;
    }

    /**
     * Authentication header map
     *
     * @param string $username
     * @param string $password
     * @return array
     * @throws AuthenticationException
     */
    private function getHeaderMap(string $username = 'customer@example.com', string $password = 'password'): array
    {
        $customerToken = $this->customerTokenService->createCustomerAccessToken($username, $password);

        return ['Authorization' => 'Bearer ' . $customerToken];
    }

    /**
     * Returns GraphQl mutation string
     *
     * @param int $wishlistItemId
     * @param int $qty
     * @param string $description
     * @param string $bundleOptions
     * @param int $wishlistId
     * @return string
     */
    private function getUpdateQuery(
        int    $wishlistItemId,
        int    $qty,
        string $description,
        string $bundleOptions,
        int    $wishlistId = 0
    ): string
    {
        return <<<MUTATION
mutation {
  updateProductsInWishlist(
    wishlistId: {$wishlistId},
    wishlistItems: [
      {
        wishlist_item_id: "{$wishlistItemId}"
        quantity: {$qty}
        description: "{$description}"
        selected_options: [
          "{$bundleOptions}"
        ]
      }
    ]
) {
    user_errors {
      code
      message
    }
    wishlist {
      id
      sharing_code
      items_count
      updated_at
      items_v2 {
        items{
          id
          quantity
          description
          ... on BundleWishlistItem {
            bundle_options {
              id
              uid
              label
              type
              values {
                id
                uid
                label
                quantity
                price
              }
            }
          }
        }
      }
    }
  }
}
MUTATION;
    }

    /**
     * Set Up
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->customerTokenService = $objectManager->get(CustomerTokenServiceInterface::class);
        $this->wishlistFactory = $objectManager->get(WishlistFactory::class);
        $this->productRepository = $objectManager->get(ProductRepositoryInterface::class);
    }
}
