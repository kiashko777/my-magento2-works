<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product\Compare;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Visitor;
use Magento\Framework\Stdlib\DateTime;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ListCompareTest extends TestCase
{
    /**
     * @var ListCompare
     */
    protected $_model;

    /**
     * @var Visitor
     */
    protected $_visitor;

    /** @var Session */
    protected $_session;

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAddProductWithSession()
    {
        $this->_session->setCustomerId(1);
        /** @var $product Product */
        $product = Bootstrap::getObjectManager()
            ->create(Product::class)
            ->load(1);
        /** @var $product2 Product */
        $product2 = Bootstrap::getObjectManager()
            ->create(Product::class)
            ->load(6);
        $products = [$product->getId(), $product2->getId()];
        $this->_model->addProducts($products);

        $this->assertTrue($this->_model->hasItems(1, $this->_visitor->getId()));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAddProductWithSessionNeg()
    {
        $this->_session->setCustomerId(1);
        $products = ['none', 99];
        $this->_model->addProducts($products);

        $this->assertFalse($this->_model->hasItems(1, $this->_visitor->getId()));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAddProductWithoutSession()
    {
        /** @var $product Product */
        $product = Bootstrap::getObjectManager()
            ->create(Product::class)
            ->load(1);
        $this->_model->addProduct($product);
        $this->assertFalse($this->_model->hasItems(1, $this->_visitor->getId()));
        $this->assertTrue($this->_model->hasItems(0, $this->_visitor->getId()));
    }

    protected function setUp(): void
    {
        /** @var $session Session */
        $this->_session = Bootstrap::getObjectManager()
            ->get(Session::class);
        $this->_visitor = Bootstrap::getObjectManager()
            ->create(Visitor::class);
        // md5() used for generate unique session identifier for test purposes.
        // phpcs:ignore Magento2.Security.InsecureFunction
        $this->_visitor->setSessionId(md5(time()) . md5(microtime()))
            ->setLastVisitAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT))
            ->save();
        $this->_model = Bootstrap::getObjectManager()
            ->create(ListCompare::class, ['customerVisitor' => $this->_visitor]);
    }

    protected function tearDown(): void
    {
        $this->_session->setCustomerId(null);
    }
}
