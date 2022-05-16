<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Layer\Filter;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Request;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Price.
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
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    public function testApplyNothing()
    {
        $this->assertEmpty($this->_model->getData('price_range'));
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request Request */
        $request = $objectManager->get(Request::class);
        $this->_model->apply(
            $request,
            Bootstrap::getObjectManager()->get(
                LayoutInterface::class
            )->createBlock(
                Text::class
            )
        );

        $this->assertEmpty($this->_model->getData('price_range'));
    }

    public function testApplyInvalid()
    {
        $this->assertEmpty($this->_model->getData('price_range'));
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request Request */
        $request = $objectManager->get(Request::class);
        $request->setParam('price', 'non-numeric');
        $this->_model->apply(
            $request,
            Bootstrap::getObjectManager()->get(
                LayoutInterface::class
            )->createBlock(
                Text::class
            )
        );

        $this->assertEmpty($this->_model->getData('price_range'));
    }

    /**
     * @magentoConfigFixture current_store catalog/layered_navigation/price_range_calculation manual
     */
    public function testApplyManual()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request Request */
        $request = $objectManager->get(Request::class);
        $request->setParam('price', '10-20');
        $this->_model->apply(
            $request,
            Bootstrap::getObjectManager()->get(
                LayoutInterface::class
            )->createBlock(
                Text::class
            )
        );
    }

    public function testGetSetCustomerGroupId()
    {
        $this->assertEquals(
            $this->groupManagement->getNotLoggedInGroup()->getId(),
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

        $this->assertEquals($currencyRate, $this->_model->getData('currency_rate'));
    }

    protected function setUp(): void
    {
        $category = Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Category::class
        );
        $category->load(4);
        $layer = Bootstrap::getObjectManager()
            ->get(\Magento\Catalog\Model\Layer\Category::class);
        $layer->setCurrentCategory($category);
        $this->_model = Bootstrap::getObjectManager()
            ->create(Price::class, ['layer' => $layer]);
        $this->groupManagement = Bootstrap::getObjectManager()
            ->get(GroupManagementInterface::class);
    }
}
