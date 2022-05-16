<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Wishlist\Helper;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Session;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

class DataTest extends AbstractController
{
    /**
     * @var Session
     */
    protected $_customerSession;
    /**
     * @var Data
     */
    private $_wishlistHelper;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function testGetAddParams()
    {
        $product = $this->objectManager->get(Product::class);
        $product->setId(11);
        $json = $this->_wishlistHelper->getAddParams($product);
        $params = (array)json_decode($json);
        $data = (array)$params['data'];
        $this->assertEquals('11', $data['product']);
        $this->assertArrayHasKey('uenc', $data);
        $this->assertStringEndsWith('wishlist/index/add/', $params['action']);
    }

    public function testGetMoveFromCartParams()
    {
        $json = $this->_wishlistHelper->getMoveFromCartParams(11);
        $params = (array)json_decode($json);
        $data = (array)$params['data'];
        $this->assertEquals('11', $data['item']);
        $this->assertArrayHasKey('uenc', $data);
        $this->assertStringEndsWith('wishlist/index/fromcart/', $params['action']);
    }

    public function testGetUpdateParams()
    {
        $product = $this->objectManager->get(Product::class);
        $product->setId(11);
        $product->setWishlistItemId(15);
        $json = $this->_wishlistHelper->getUpdateParams($product);
        $params = (array)json_decode($json);
        $data = (array)$params['data'];
        $this->assertEquals('11', $data['product']);
        $this->assertEquals('15', $data['id']);
        $this->assertArrayHasKey('uenc', $data);
        $this->assertStringEndsWith('wishlist/index/updateItemOptions/', $params['action']);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testWishlistCustomer()
    {
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = $this->objectManager->create(
            CustomerRepositoryInterface::class
        );
        $customer = $customerRepository->getById(1);

        $this->_wishlistHelper->setCustomer($customer);
        $this->assertSame($customer, $this->_wishlistHelper->getCustomer());

        $this->_wishlistHelper = null;
        /** @var Data wishlistHelper */
        $this->_wishlistHelper = $this->objectManager->get(Data::class);

        $this->_customerSession->loginById(1);
        $this->assertEquals($customer, $this->_wishlistHelper->getCustomer());

        /** @var View $customerViewHelper */
        $customerViewHelper = $this->objectManager->create(View::class);
        $this->assertEquals($customerViewHelper->getCustomerName($customer), $this->_wishlistHelper->getCustomerName());
    }

    /**
     * Get required instance
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->_wishlistHelper = $this->objectManager->get(Data::class);
        $this->_customerSession = $this->objectManager->get(Session::class);
    }

    /**
     * Clear wishlist helper property
     */
    protected function tearDown(): void
    {
        $this->_wishlistHelper = null;
        if ($this->_customerSession->isLoggedIn()) {
            $this->_customerSession->logout();
        }
    }
}
