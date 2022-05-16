<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Block\Adminhtml\Sales\Sales;

use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class GridTest extends TestCase
{
    /**
     * @return string
     */
    public function testGetResourceCollectionNameNormal()
    {
        $block = $this->_createBlock();
        $normalCollection = $block->getResourceCollectionName();
        $this->assertTrue(class_exists($normalCollection));

        return $normalCollection;
    }

    /**
     * Creates and inits block
     *
     * @param string|null $reportType
     * @return Grid
     */
    protected function _createBlock($reportType = null)
    {
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Grid::class
        );

        $filterData = new DataObject();
        if ($reportType) {
            $filterData->setReportType($reportType);
        }
        $block->setFilterData($filterData);

        return $block;
    }

    /**
     * @depends testGetResourceCollectionNameNormal
     * @param string $normalCollection
     */
    public function testGetResourceCollectionNameWithFilter($normalCollection)
    {
        $block = $this->_createBlock('updated_at_order');
        $filteredCollection = $block->getResourceCollectionName();
        $this->assertTrue(class_exists($filteredCollection));

        $this->assertNotEquals($normalCollection, $filteredCollection);
    }

    /**
     * Check that grid does not contain unnecessary totals row
     *
     * @param $from string
     * @param $to string
     * @param $expectedResult bool
     *
     * @dataProvider getCountTotalsDataProvider
     * @magentoDataFixture Magento/Reports/_files/orders.php
     */
    public function testGetCountTotals($from, $to, $expectedResult)
    {
        $block = $this->_createBlock();
        $filterData = new DataObject();

        $filterData->setReportType('updated_at_order');
        $filterData->setPeriodType('day');
        $filterData->setData('from', $from);
        $filterData->setData('to', $to);
        $block->setFilterData($filterData);

        $block->toHtml();
        $this->assertEquals($expectedResult, $block->getCountTotals());
    }

    /**
     * Data provider for testGetCountTotals
     *
     * @return array
     */
    public function getCountTotalsDataProvider()
    {
        $time = time();
        return [
            [date("Y-m-d", $time + 48 * 60 * 60), date("Y-m-d", $time + 72 * 60 * 60), false],
            [date("Y-m-d", $time - 48 * 60 * 60), date("Y-m-d", $time + 48 * 60 * 60), true],
            [null, null, false],
        ];
    }
}
