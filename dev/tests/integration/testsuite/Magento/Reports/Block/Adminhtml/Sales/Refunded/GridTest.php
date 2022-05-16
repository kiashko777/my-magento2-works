<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Block\Adminhtml\Sales\Refunded;

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
        $block = $this->_createBlock('created_at_refunded');
        $filteredCollection = $block->getResourceCollectionName();
        $this->assertTrue(class_exists($filteredCollection));

        $this->assertNotEquals($normalCollection, $filteredCollection);
    }
}
