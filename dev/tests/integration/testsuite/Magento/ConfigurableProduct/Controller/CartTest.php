<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Checkout\Controller\Cart
 */

namespace Magento\ConfigurableProduct\Controller;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Message\MessageInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\TestFramework\Helper\Xpath;
use Magento\TestFramework\TestCase\AbstractController;
use ReflectionObject;

class CartTest extends AbstractController
{
    /**
     * Test for \Magento\Checkout\Controller\Cart::configureAction() with configurable product
     *
     * @magentoDataFixture Magento/ConfigurableProduct/_files/quote_with_configurable_product.php
     */
    public function testConfigureActionWithConfigurableProduct()
    {
        /** @var $session Session */
        $session = $this->_objectManager->create(Session::class);

        $quoteItem = $this->_getQuoteItemIdByProductId($session->getQuote(), 1);
        $this->assertNotNull($quoteItem, 'Cannot get quote item for configurable product');

        $this->dispatch(
            'checkout/cart/configure/id/' . $quoteItem->getId() . '/product_id/' . $quoteItem->getProduct()->getId()
        );
        $response = $this->getResponse();

        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//button[@type="submit" and @title="Update Cart"]',
                $response->getBody()
            ),
            'Response for configurable product doesn\'t contain "Update Cart" button'
        );

        $this->assertEquals(
            1,
            Xpath::getElementsCountForXpath(
                '//select[contains(@class,"super-attribute-select")]',
                $response->getBody()
            ),
            'Response for configurable product doesn\'t contain select for super attribute'
        );
    }

    /**
     * Gets \Magento\Quote\Model\Quote\Item from \Magento\Quote\Model\Quote by product id
     *
     * @param Quote $quote
     * @param mixed $productId
     * @return Item|null
     */
    private function _getQuoteItemIdByProductId(Quote $quote, $productId)
    {
        /** @var $quoteItems Item[] */
        $quoteItems = $quote->getAllItems();
        foreach ($quoteItems as $quoteItem) {
            if ($productId == $quoteItem->getProductId()) {
                return $quoteItem;
            }
        }
        return null;
    }

    /**
     * Test for \Magento\Checkout\Controller\Cart\CouponPost::execute() with configurable product with last option
     * Covers MAGETWO-60352
     *
     * @magentoDataFixture Magento/ConfigurableProduct/_files/quote_with_configurable_product_last_variation.php
     */
    public function testExecuteForConfigurableLastOption()
    {
        /** @var $session Session */
        $session = $this->_objectManager->create(Session::class);
        $quote = $session->getQuote();
        $quote->setData('trigger_recollect', 1)->setTotalsCollectedFlag(true);
        $inputData = [
            'remove' => 0,
            'coupon_code' => 'test'
        ];
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue($inputData);
        $this->dispatch(
            'checkout/cart/couponPost/'
        );

        $this->assertSessionMessages(
            $this->equalTo(['The coupon code &quot;test&quot; is not valid.']),
            MessageInterface::TYPE_ERROR
        );
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
