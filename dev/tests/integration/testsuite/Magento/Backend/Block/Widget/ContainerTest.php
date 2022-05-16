<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Widget;

use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class ContainerTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
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
                    Container::PARAM_CONTROLLER => 'one',
                    Container::PARAM_HEADER_TEXT => 'two',
                ]
            ]
        );
        $this->assertStringEndsWith('one', $block->getHeaderCssClass());
        $this->assertStringContainsString('two', $block->getHeaderText());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetButtonsHtml()
    {
        $titles = [1 => 'Title 1', 'Title 2', 'Title 3'];
        $block = $this->_buildBlock($titles);
        $html = $block->getButtonsHtml('header');

        $this->assertStringContainsString('<button', $html);
        foreach ($titles as $title) {
            $this->assertStringContainsString($title, $html);
        }
    }

    /**
     * Composes a container with several buttons in it
     *
     * @param array $titles
     * @param string $blockName
     * @return Container
     */
    protected function _buildBlock($titles, $blockName = 'block')
    {
        /** @var $layout LayoutInterface */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Container */
        $block = $layout->createBlock(Container::class, $blockName);
        foreach ($titles as $id => $title) {
            $block->addButton($id, ['title' => $title], 0, 0, 'header');
        }
        $block->setLayout($layout);
        return $block;
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testUpdateButton()
    {
        $originalTitles = [1 => 'Title 1', 'Title 2', 'Title 3'];
        $newTitles = [1 => 'Button A', 'Button B', 'Button C'];

        $block = $this->_buildBlock($originalTitles);
        foreach ($newTitles as $id => $newTitle) {
            $block->updateButton($id, 'title', $newTitle);
        }
        $html = $block->getButtonsHtml('header');
        foreach ($newTitles as $newTitle) {
            $this->assertStringContainsString($newTitle, $html);
        }
    }
}
