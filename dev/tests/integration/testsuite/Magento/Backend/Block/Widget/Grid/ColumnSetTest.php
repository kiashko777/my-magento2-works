<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Widget\Grid;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class ColumnSetTest extends TestCase
{
    /**
     * @var ColumnSet
     */
    protected $_block;

    /**
     * @var MockObject
     */
    protected $_layoutMock;

    /**
     * @var MockObject
     */
    protected $_columnMock;

    public function testBeforeToHtmlAddsClassToLastColumn()
    {
        $this->_columnMock->expects($this->any())->method('addHeaderCssClass')->with($this->equalTo('last'));
        $this->_block->toHtml();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->_columnMock = $this->createPartialMock(
            Column::class,
            ['setSortable', 'setRendererType', 'setFilterType', 'addHeaderCssClass', 'setGrid']
        );
        $this->_layoutMock = $this->createMock(Layout::class);
        $this->_layoutMock->expects(
            $this->any()
        )->method(
            'getChildBlocks'
        )->willReturn(
            [$this->_columnMock]
        );

        $context = Bootstrap::getObjectManager()->create(
            Context::class,
            ['layout' => $this->_layoutMock]
        );
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            ColumnSet::class,
            '',
            ['context' => $context]
        );
        $this->_block->setTemplate(null);
    }
}
