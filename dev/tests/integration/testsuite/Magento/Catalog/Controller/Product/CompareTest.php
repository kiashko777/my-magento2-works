<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Controller\Product;

use Exception;
use Laminas\Stdlib\ParametersFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Compare\Item;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Visitor;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Test compare product.
 *
 * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
 * @magentoDbIsolation disabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CompareTest extends AbstractController
{
    /** @var ProductRepository */
    protected $productRepository;

    /** @var FormKey */
    private $formKey;

    /** @var Session */
    private $customerSession;

    /** @var Visitor */
    private $visitor;

    /** @var ParametersFactory */
    private $parametersFactory;

    /** @var Registry */
    private $registry;

    /**
     * Test adding product to compare list.
     *
     * @return void
     */
    public function testAddAction(): void
    {
        $this->_requireVisitorWithNoProducts();
        $product = $this->productRepository->get('simple_product_1');
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch(
            sprintf(
                'catalog/product_compare/add/product/%s/form_key/%s?nocookie=1',
                $product->getEntityId(),
                $this->formKey->getFormKey()
            )
        );

        $this->assertSessionMessages(
            $this->equalTo(
                [
                    'You added product Simple Products 1 Name to the ' .
                    '<a href="http://localhost/index.php/catalog/product_compare/">comparison list</a>.'
                ]
            ),
            MessageInterface::TYPE_SUCCESS
        );

        $this->assertRedirect();

        $this->_assertCompareListEquals([$product->getEntityId()]);
    }

    /**
     * Preparing compare list.
     *
     * @return void
     */
    protected function _requireVisitorWithNoProducts(): void
    {
        /** @var $visitor Visitor */
        $visitor = Bootstrap::getObjectManager()
            ->create(Visitor::class);

        // phpcs:ignore
        $visitor->setSessionId(md5(time()) . md5(microtime()))
            ->setLastVisitAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT))
            ->save();

        Bootstrap::getObjectManager()->get(
            Visitor::class
        )->load(
            $visitor->getId()
        );

        $this->_assertCompareListEquals([]);
    }

    /**
     * Assert that current visitor has exactly expected products in compare list
     *
     * @param array $expectedProductIds
     * @return void
     */
    protected function _assertCompareListEquals(array $expectedProductIds): void
    {
        /** @var $compareItems Collection */
        $compareItems = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $compareItems->useProductItem();
        // important
        $compareItems->setVisitorId(
            Bootstrap::getObjectManager()->get(
                Visitor::class
            )->getId()
        );
        $actualProductIds = [];
        foreach ($compareItems as $compareItem) {
            /** @var $compareItem Item */
            $actualProductIds[] = $compareItem->getProductId();
        }
        $this->assertEquals($expectedProductIds, $actualProductIds, "Products in current visitor's compare list.");
    }

    /**
     * Test adding disabled product to compare list.
     *
     * @return void
     */
    public function testAddActionForDisabledProduct(): void
    {
        $this->_requireVisitorWithNoProducts();
        /** @var Product $product */
        $product = $this->setProductDisabled('simple_product_1');

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch(
            sprintf(
                'catalog/product_compare/add/product/%s/form_key/%s?nocookie=1',
                $product->getEntityId(),
                $this->formKey->getFormKey()
            )
        );

        $this->assertRedirect();

        $this->_assertCompareListEquals([]);
    }

    /**
     * Set product status disabled.
     *
     * @param string $sku
     * @return ProductInterface
     */
    private function setProductDisabled(string $sku): ProductInterface
    {
        $product = $this->productRepository->get($sku);
        $product->setStatus(Status::STATUS_DISABLED)
            ->save();

        return $product;
    }

    /**
     * Test removing a product from compare list.
     *
     * @return void
     */
    public function testRemoveAction(): void
    {
        $this->_requireVisitorWithTwoProducts();
        $product = $this->productRepository->get('simple_product_2');
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch('catalog/product_compare/remove/product/' . $product->getEntityId());

        $this->assertSessionMessages(
            $this->equalTo(['You removed product Simple Products 2 Name from the comparison list.']),
            MessageInterface::TYPE_SUCCESS
        );

        $this->assertRedirect();
        $restProduct = $this->productRepository->get('simple_product_1');
        $this->_assertCompareListEquals([$restProduct->getEntityId()]);
    }

    /**
     * Preparing compare list.
     *
     * @return void
     */
    protected function _requireVisitorWithTwoProducts(): void
    {
        /** @var $visitor Visitor */
        $visitor = Bootstrap::getObjectManager()
            ->create(Visitor::class);
        // phpcs:ignore
        $visitor->setSessionId(md5(time()) . md5(microtime()))
            ->setLastVisitAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT))
            ->save();

        /** @var $item Item */
        $item = Bootstrap::getObjectManager()->create(
            Item::class
        );
        $firstProductEntityId = $this->productRepository->get('simple_product_1')->getEntityId();
        $secondProductEntityId = $this->productRepository->get('simple_product_2')->getEntityId();
        $item->setVisitorId($visitor->getId())->setProductId($firstProductEntityId)->save();

        /** @var $item Item */
        $item = Bootstrap::getObjectManager()->create(
            Item::class
        );
        $item->setVisitorId($visitor->getId())->setProductId($secondProductEntityId)->save();

        Bootstrap::getObjectManager()->get(
            Visitor::class
        )->load(
            $visitor->getId()
        );

        $this->_assertCompareListEquals([$firstProductEntityId, $secondProductEntityId]);
    }

    /**
     * Test removing a disabled product from compare list.
     *
     * @return void
     */
    public function testRemoveActionForDisabledProduct(): void
    {
        $this->_requireVisitorWithTwoProducts();
        /** @var Product $product */
        $product = $this->setProductDisabled('simple_product_1');
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch('catalog/product_compare/remove/product/' . $product->getEntityId());

        $this->assertRedirect();
        $restProduct = $this->productRepository->get('simple_product_2');
        $this->_assertCompareListEquals([$product->getEntityId(), $restProduct->getEntityId()]);
    }

    /**
     * Test removing a product from compare list of a registered customer.
     *
     * @return void
     */
    public function testRemoveActionWithSession(): void
    {
        $this->_requireCustomerWithTwoProducts();
        $product = $this->productRepository->get('simple_product_1');
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch('catalog/product_compare/remove/product/' . $product->getEntityId());
        $secondProduct = $this->productRepository->get('simple_product_2');

        $this->assertSessionMessages(
            $this->equalTo(['You removed product Simple Products 1 Name from the comparison list.']),
            MessageInterface::TYPE_SUCCESS
        );

        $this->assertRedirect();

        $this->_assertCompareListEquals([$secondProduct->getEntityId()]);
    }

    /**
     * Preparing a compare list.
     *
     * @return void
     */
    protected function _requireCustomerWithTwoProducts(): void
    {
        $customer = Bootstrap::getObjectManager()
            ->create(Customer::class);
        /** @var Customer $customer */
        $customer
            ->setWebsiteId(1)
            ->setId(1)
            ->setEntityTypeId(1)
            ->setAttributeSetId(1)
            ->setEmail('customer@example.com')
            ->setPassword('password')
            ->setGroupId(1)
            ->setStoreId(1)
            ->setIsActive(1)
            ->setFirstname('Firstname')
            ->setLastname('Lastname')
            ->setDefaultBilling(1)
            ->setDefaultShipping(1);
        $customer->isObjectNew(true);
        $customer->save();

        /** @var $session Session */
        $session = Bootstrap::getObjectManager()
            ->get(Session::class);
        $session->setCustomerId(1);

        /** @var $visitor Visitor */
        $visitor = Bootstrap::getObjectManager()
            ->create(Visitor::class);
        // phpcs:ignore
        $visitor->setSessionId(md5(time()) . md5(microtime()))
            ->setLastVisitAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT))
            ->save();

        $firstProductEntityId = $this->productRepository->get('simple_product_1')->getEntityId();
        $secondProductEntityId = $this->productRepository->get('simple_product_2')->getEntityId();

        /** @var $item Item */
        $item = Bootstrap::getObjectManager()
            ->create(Item::class);
        $item->setVisitorId($visitor->getId())
            ->setCustomerId(1)
            ->setProductId($firstProductEntityId)
            ->save();

        /** @var $item Item */
        $item = Bootstrap::getObjectManager()
            ->create(Item::class);
        $item->setVisitorId($visitor->getId())
            ->setProductId($secondProductEntityId)
            ->save();

        Bootstrap::getObjectManager()->get(Visitor::class)
            ->load($visitor->getId());

        $this->_assertCompareListEquals([$firstProductEntityId, $secondProductEntityId]);
    }

    /**
     * Test getting a list of compared product.
     *
     * @return void
     */
    public function testIndexActionDisplay(): void
    {
        $this->_requireVisitorWithTwoProducts();

        $layout = $this->_objectManager->get(LayoutInterface::class);
        $layout->setIsCacheable(false);

        $this->dispatch('catalog/product_compare/index');

        $responseBody = $this->getResponse()->getBody();

        $this->assertStringContainsString('Products Comparison List', $responseBody);

        $this->assertStringContainsString('simple_product_1', $responseBody);
        $this->assertStringContainsString('Simple Products 1 Name', $responseBody);
        $this->assertStringContainsString('Simple Products 1 Full Description', $responseBody);
        $this->assertStringContainsString('Simple Products 1 Short Description', $responseBody);
        $this->assertStringContainsString('$1,234.56', $responseBody);

        $this->assertStringContainsString('simple_product_2', $responseBody);
        $this->assertStringContainsString('Simple Products 2 Name', $responseBody);
        $this->assertStringContainsString('Simple Products 2 Full Description', $responseBody);
        $this->assertStringContainsString('Simple Products 2 Short Description', $responseBody);
        $this->assertStringContainsString('$987.65', $responseBody);
    }

    /**
     * Test clearing a list of compared products.
     *
     * @return void
     */
    public function testClearAction(): void
    {
        $this->_requireVisitorWithTwoProducts();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch('catalog/product_compare/clear');

        $this->assertSessionMessages(
            $this->equalTo(['You cleared the comparison list.']),
            MessageInterface::TYPE_SUCCESS
        );

        $this->assertRedirect();

        $this->_assertCompareListEquals([]);
    }

    /**
     * Test escaping a session message.
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple_xss.php
     * @return void
     */
    public function testRemoveActionProductNameXss(): void
    {
        $this->_prepareCompareListWithProductNameXss();
        $product = $this->productRepository->get('product-with-xss');
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch('catalog/product_compare/remove/product/' . $product->getEntityId() . '?nocookie=1');

        $this->assertSessionMessages(
            $this->equalTo(
                ['You removed product &lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt; from the comparison list.']
            ),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * Preparing compare list.
     *
     * @return void
     */
    protected function _prepareCompareListWithProductNameXss(): void
    {
        /** @var $visitor Visitor */
        $visitor = Bootstrap::getObjectManager()
            ->create(Visitor::class);
        /** @var DateTime $dateTime */
        // phpcs:ignore
        $visitor->setSessionId(md5(time()) . md5(microtime()))
            ->setLastVisitAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT))
            ->save();
        /** @var $item Item */
        $item = Bootstrap::getObjectManager()->create(
            Item::class
        );
        $firstProductEntityId = $this->productRepository->get('product-with-xss')->getEntityId();
        $item->setVisitorId($visitor->getId())->setProductId($firstProductEntityId)->save();
        Bootstrap::getObjectManager()->get(
            Visitor::class
        )->load(
            $visitor->getId()
        );
    }

    /**
     * Test removing a product wich does not exist from compare list.
     *
     * @return void
     */
    public function testRemoveActionWithNonExistentProduct(): void
    {
        $this->_requireVisitorWithTwoProducts();
        $removedProduct = $this->productRepository->get('simple_product_1');
        $redirectUrl = 'http://localhost/index.php/catalog/product_compare/index';
        $this->assertTrue($this->deleteProduct($removedProduct), "The product must be removed.");

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setParams(['product' => $removedProduct->getId()]);
        $server = $this->getRequest()->getServer();
        $server['HTTP_REFERER'] = $redirectUrl;
        $this->getRequest()->setServer($server);
        $this->dispatch('catalog/product_compare/remove/');

        $this->assertSessionMessages($this->isEmpty());
        $this->assertRedirect($this->equalTo($redirectUrl));
        $restProduct = $this->productRepository->get('simple_product_2');
        $this->_assertCompareListEquals([$restProduct->getId()]);
    }

    /**
     * Delete product in secure area
     *
     * @param ProductInterface $product
     * @return bool
     */
    private function deleteProduct(ProductInterface $product): bool
    {
        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', true);

        try {
            $result = $this->productRepository->delete($product);
        } catch (Exception $e) {
            $result = false;
        }

        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', false);

        return $result;
    }

    /**
     * Add not existing product to list of compared.
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @return void
     */
    public function testAddNotExistingProductToCompareList(): void
    {
        $this->customerSession->loginById(1);
        $this->prepareReferer();
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setParams(['product' => 787586534]);
        $this->dispatch('catalog/product_compare/add/');
        $this->assertSessionMessages($this->isEmpty());
        $this->_assertCompareListEquals([]);
        $this->assertRedirect($this->stringContains('not_existing'));
    }

    /**
     * Prepare referer to test.
     *
     * @return void
     */
    private function prepareReferer(): void
    {
        $parameters = $this->parametersFactory->create();
        $parameters->set('HTTP_REFERER', 'http://localhost/not_existing');
        $this->getRequest()->setServer($parameters);
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->productRepository = $this->_objectManager->get(ProductRepository::class);
        $this->customerSession = $this->_objectManager->get(Session::class);
        $this->visitor = $this->_objectManager->get(Visitor::class);
        $this->parametersFactory = $this->_objectManager->get(ParametersFactory::class);
        $this->registry = $this->_objectManager->get(Registry::class);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->customerSession->logout();
        $this->visitor->setId(null);

        parent::tearDown();
    }
}
