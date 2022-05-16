<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Block\Adminhtml\Sales\Bestsellers;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class GridTest extends TestCase
{
    /**
     * @var Grid
     */
    protected $_block;

    public function testGetResourceCollectionName()
    {
        $collectionName = $this->_block->getResourceCollectionName();
        $this->assertTrue(class_exists($collectionName));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Grid::class
        );
    }
}
