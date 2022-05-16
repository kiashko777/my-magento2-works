<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magento\Integration\Block\Adminhtml\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Integration\Block\Adminhtml\Integration\Grid
 *
 * @magentoAppArea Adminhtml
 */
class GridTest extends TestCase
{
    /**
     * @var Grid
     */
    protected $gridBlock;

    public function testGetRowClickCallback()
    {
        $this->assertEquals('', $this->gridBlock->getRowClickCallback());
    }

    public function testGetRowInitCallback()
    {
        $this->assertEquals('', $this->gridBlock->getRowInitCallback());
    }

    protected function setUp(): void
    {
        $this->gridBlock = Bootstrap::getObjectManager()
            ->create(Grid::class);
    }
}
