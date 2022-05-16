<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Widget\Grid;

use Magento\Backend\Block\Widget\Grid;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class ContainerTest extends TestCase
{
    public function testPseudoConstruct()
    {
        /** @var $block Container */
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Container::class,
            '',
            [
                'data' => [
                    \Magento\Backend\Block\Widget\Container::PARAM_CONTROLLER => 'widget',
                    \Magento\Backend\Block\Widget\Container::PARAM_HEADER_TEXT => 'two',
                    Container::PARAM_BLOCK_GROUP => 'Magento_Backend',
                    Container::PARAM_BUTTON_NEW => 'four',
                    Container::PARAM_BUTTON_BACK => 'five',
                ]
            ]
        );
        $this->assertStringEndsWith('widget', $block->getHeaderCssClass());
        $this->assertStringContainsString('two', $block->getHeaderText());
        $this->assertInstanceOf(Grid::class, $block->getChildBlock('grid'));
        $this->assertEquals('four', $block->getAddButtonLabel());
        $this->assertEquals('five', $block->getBackButtonLabel());
    }
}
