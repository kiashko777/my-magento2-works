<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Layer\Filter\Price;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Category;
use Magento\Catalog\Model\Layer\Filter\Price;
use Magento\Catalog\Model\Layer\State;
use Magento\CatalogSearch\Model\Price\Interval;
use Magento\Framework\DataObject;
use Magento\Framework\Search\Dynamic\Algorithm;
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Request;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Price.
 */
class AlgorithmAdvancedTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/Price/_files/products_advanced.php
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @covers \Magento\Framework\Search\Dynamic\Algorithm::calculateSeparators
     */
    public function testWithoutLimits()
    {
        $layer = $this->createLayer();
        $priceResource = $this->createPriceResource($layer);
        $interval = $this->createInterval($priceResource);

        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request Request */
        $request = $objectManager->get(Request::class);
        $request->setParam('price', null);
        $model = $this->_prepareFilter($layer, $priceResource);
        $this->assertEquals(
            [
                0 => ['from' => 0, 'to' => 20, 'count' => 3],
                1 => ['from' => 20, 'to' => '', 'count' => 4],
            ],
            $model->calculateSeparators($interval)
        );
    }

    /**
     * @return Layer
     */
    protected function createLayer()
    {
        $layer = Bootstrap::getObjectManager()
            ->create(Category::class);
        $layer->setCurrentCategory(4);
        $layer->setState(
            Bootstrap::getObjectManager()->create(State::class)
        );
        return $layer;
    }

    /**
     * @param $layer
     * @return \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price
     */
    protected function createPriceResource($layer)
    {
        return Bootstrap::getObjectManager()
            ->create(\Magento\Catalog\Model\ResourceModel\Layer\Filter\Price::class, ['layer' => $layer]);
    }

    /**
     * @param $priceResource
     * @return Interval
     */
    protected function createInterval($priceResource)
    {
        return Bootstrap::getObjectManager()
            ->create(Interval::class, ['resource' => $priceResource]);
    }

    /**
     * Prepare price filter model
     *
     * @param Layer $layer
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $priceResource
     * @param Request|null $request
     * @return Algorithm
     * @internal param \Magento\CatalogSearch\Model\Price\Interval $interval
     */
    protected function _prepareFilter($layer, $priceResource, $request = null)
    {
        /** @var Algorithm $model */
        $model = Bootstrap::getObjectManager()
            ->create(Algorithm::class);
        /** @var $filter Price */
        $filter = Bootstrap::getObjectManager()
            ->create(
                Price::class,
                ['layer' => $layer, 'resource' => $priceResource, 'priceAlgorithm' => $model]
            );
        $filter->setLayer($layer)->setAttributeModel(new DataObject(['attribute_code' => 'price']));
        if ($request !== null) {
            $filter->apply(
                $request,
                Bootstrap::getObjectManager()->get(
                    LayoutInterface::class
                )->createBlock(
                    Text::class
                )
            );
            $interval = $filter->getInterval();
            if ($interval) {
                $model->setLimits($interval[0], $interval[1]);
            }
        }
        $collection = $layer->getProductCollection();
        $model->setStatistics(
            $collection->getMinPrice(),
            $collection->getMaxPrice(),
            $collection->getPriceStandardDeviation(),
            $collection->getPricesCount()
        );
        return $model;
    }

    /**
     * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/Price/_files/products_advanced.php
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @covers \Magento\Framework\Search\Dynamic\Algorithm::calculateSeparators
     */
    public function testWithLimits()
    {
        $this->markTestIncomplete('Bug MAGE-6561');

        $layer = $this->createLayer();
        $priceResource = $this->createPriceResource($layer);
        $interval = $this->createInterval($priceResource);

        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var $request Request */
        $request = $objectManager->get(Request::class);
        $request->setParam('price', '10-100');
        $model = $this->_prepareFilter($layer, $priceResource, $request);
        $this->assertEquals(
            [
                0 => ['from' => 10, 'to' => 20, 'count' => 2],
                1 => ['from' => 20, 'to' => 100, 'count' => 2],
            ],
            $model->calculateSeparators($interval)
        );
    }
}
