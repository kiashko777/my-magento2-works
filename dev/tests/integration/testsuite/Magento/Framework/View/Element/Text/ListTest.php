<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\View\Element\Text;

use Magento\Framework\View\Element\Text;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ListTest extends TestCase
{
    /**
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @var ListText
     */
    protected $_block;

    public function testToHtml()
    {
        $children = [
            ['block1', Text::class, 'text1'],
            ['block2', Text::class, 'text2'],
            ['block3', Text::class, 'text3'],
        ];
        foreach ($children as $child) {
            $this->_layout->addBlock($child[1], $child[0], $this->_block->getNameInLayout())->setText($child[2]);
        }
        $html = $this->_block->toHtml();
        $this->assertEquals('text1text2text3', $html);
    }

    public function testToHtmlWithContainer()
    {
        $listName = $this->_block->getNameInLayout();
        $block1 = $this->_layout->addBlock(Text::class, '', $listName);
        $this->_layout->addContainer('container', 'Container', [], $listName);
        $block2 = $this->_layout->addBlock(Text::class, '', 'container');
        $block3 = $this->_layout->addBlock(Text::class, '', $listName);
        $block1->setText('text1');
        $block2->setText('text2');
        $block3->setText('text3');
        $html = $this->_block->toHtml();
        $this->assertEquals('text1text2text3', $html);
    }

    protected function setUp(): void
    {
        $this->_layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        $this->_block = $this->_layout->createBlock(ListText::class);
    }
}
