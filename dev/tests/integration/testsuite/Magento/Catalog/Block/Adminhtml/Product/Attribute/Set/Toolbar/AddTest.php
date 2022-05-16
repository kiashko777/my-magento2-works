<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Toolbar;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class AddTest extends TestCase
{
    public function testToHtmlFormId()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );

        $block = $layout->addBlock(Add::class, 'block');
        $block->setArea('Adminhtml')->unsetChild('setForm');

        $childBlock = $layout->addBlock(Template::class, 'setForm', 'block');
        $form = new DataObject();
        $childBlock->setForm($form);

        $expectedId = '12121212';
        $this->assertStringNotContainsString($expectedId, $block->toHtml());
        $form->setId($expectedId);
        $this->assertStringContainsString($expectedId, $block->toHtml());
    }
}
