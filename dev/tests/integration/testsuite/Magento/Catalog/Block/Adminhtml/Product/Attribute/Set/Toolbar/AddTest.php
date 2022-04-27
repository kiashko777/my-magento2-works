<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Toolbar;

/**
 * @magentoAppArea Adminhtml
 */
class AddTest extends \PHPUnit\Framework\TestCase
{
    public function testToHtmlFormId()
    {
        /** @var $layout \Magento\Framework\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\View\LayoutInterface::class
        );

        $block = $layout->addBlock(\Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Toolbar\Add::class, 'block');
        $block->setArea('Adminhtml')->unsetChild('setForm');

        $childBlock = $layout->addBlock(\Magento\Framework\View\Element\Template::class, 'setForm', 'block');
        $form = new \Magento\Framework\DataObject();
        $childBlock->setForm($form);

        $expectedId = '12121212';
        $this->assertStringNotContainsString($expectedId, $block->toHtml());
        $form->setId($expectedId);
        $this->assertStringContainsString($expectedId, $block->toHtml());
    }
}
