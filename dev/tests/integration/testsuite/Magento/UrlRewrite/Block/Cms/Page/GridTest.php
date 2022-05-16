<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\UrlRewrite\Block\Cms\Page;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\UrlRewrite\Block\Cms\Page\Grid
 * @magentoAppArea Adminhtml
 */
class GridTest extends TestCase
{
    /**
     * Test prepare grid
     */
    public function testPrepareGrid()
    {
        /** @var Grid $gridBlock */
        $gridBlock = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Grid::class
        );
        $gridBlock->toHtml();

        foreach (['title', 'identifier', 'is_active'] as $key) {
            $this->assertInstanceOf(
                Column::class,
                $gridBlock->getColumn($key),
                'Column with key "' . $key . '" is invalid'
            );
        }

        $this->assertStringStartsWith('http://localhost/index.php', $gridBlock->getGridUrl(), 'Grid URL is invalid');

        $row = new DataObject(['id' => 1]);
        $this->assertStringStartsWith(
            'http://localhost/index.php/backend/admin/index/edit/cms_page/1',
            $gridBlock->getRowUrl($row),
            'Grid row URL is invalid'
        );

        $this->assertEmpty($gridBlock->getMassactionBlock()->getItems(), 'Grid should not have mass action items');
        $this->assertTrue($gridBlock->getUseAjax(), '"use_ajax" value of grid is incorrect');
    }

    /**
     * Test prepare grid when there is more than one store
     *
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     */
    public function testPrepareGridForMultipleStores()
    {
        /** @var Grid $gridBlock */
        $gridBlock = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Grid::class
        );
        $gridBlock->toHtml();
        $this->assertInstanceOf(
            Column::class,
            $gridBlock->getColumn('store_id'),
            'When there is more than one store column with key "store_id" should be present'
        );
    }
}
