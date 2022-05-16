<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\System\Store;

use Magento\Framework\DataObject;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class DeleteTest extends TestCase
{
    public function testGetHeaderText()
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Delete */
        $block = $layout->createBlock(Delete::class, 'block');

        $dataObject = new DataObject();
        $form = $block->getChildBlock('form');
        $form->setDataObject($dataObject);

        $expectedValue = 'header_text_test';
        $this->assertStringNotContainsString($expectedValue, (string)$block->getHeaderText());

        $dataObject->setName($expectedValue);
        $this->assertStringContainsString($expectedValue, (string)$block->getHeaderText());
    }
}
