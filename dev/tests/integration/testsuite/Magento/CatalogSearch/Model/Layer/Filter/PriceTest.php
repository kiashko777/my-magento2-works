<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Model\Layer\Filter;

use Magento\Customer\Model\GroupManagement;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Request;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\CatalogSearch\Model\Layer\Filter\Price.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class PriceTest extends TestCase
{
    /**
     * @var Price
     */
    protected $_model;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function testApplyNothing()
    {
        $this->assertEmpty($this->_model->getData('price_range'));
        /** @var $request Request */
        $request = $this->objectManager->get(Request::class);
        $this->_model->apply($request);

        $this->assertEmpty($this->_model->getData('price_range'));
    }

    public function testApplyInvalid()
    {
        $this->assertEmpty($this->_model->getData('price_range'));
        /** @var $request Request */
        $request = $this->objectManager->get(Request::class);
        $request->setParam('price', 'non-numeric');
        $this->_model->apply($request);

        $this->assertEmpty($this->_model->getData('price_range'));
    }

    /**
     * @magentoConfigFixture current_store catalog/layered_navigation/price_range_calculation manual
     */
    public function testApplyManual()
    {
        /** @var $request Request */
        $request = $this->objectManager->get(Request::class);
        $request->setParam('price', '10-20');
        $this->_model->apply($request);
    }

    /**
     * Make sure that currency rate is used to calculate label for applied price filter
     */
    public function testApplyWithCustomCurrencyRate()
    {
        /** @var $request Request */
        $request = $this->objectManager->get(Request::class);

        $request->setParam('price', '10-20');
        $this->_model->setCurrencyRate(10);

        $this->_model->apply($request);

        $filters = $this->_model->getLayer()->getState()->getFilters();
        $this->assertArrayHasKey(0, $filters);
        $this->assertEquals(
            '<span class="price">$100.00</span> - <span class="price">$199.99</span>',
            (string)$filters[0]->getLabel()
        );
    }

    public function testGetSetCustomerGroupId()
    {
        $this->assertEquals(
            GroupManagement::NOT_LOGGED_IN_ID,
            $this->_model->getCustomerGroupId()
        );

        $customerGroupId = 123;
        $this->_model->setCustomerGroupId($customerGroupId);

        $this->assertEquals($customerGroupId, $this->_model->getCustomerGroupId());
    }

    public function testGetSetCurrencyRate()
    {
        $this->assertEquals(1, $this->_model->getCurrencyRate());

        $currencyRate = 42;
        $this->_model->setCurrencyRate($currencyRate);

        $this->assertEquals($currencyRate, $this->_model->getCurrencyRate());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $category = $this->objectManager->create(
            \Magento\Catalog\Model\Category::class
        );
        $category->load(4);
        $layer = $this->objectManager->get(\Magento\Catalog\Model\Layer\Category::class);
        $layer->setCurrentCategory($category);
        $this->_model = $this->objectManager->create(
            Price::class,
            ['layer' => $layer]
        );
    }
}
