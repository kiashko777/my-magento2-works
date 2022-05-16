<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab;

use Magento\Framework\Data\Form\Element\Select;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class MainTest extends TestCase
{
    public function testPackageThemeElement()
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)
            ->register('current_widget_instance', new DataObject());
        /** @var Main $block */
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Main::class
        );
        $block->setTemplate(null);
        $block->toHtml();
        $element = $block->getForm()->getElement('theme_id');
        $this->assertInstanceOf(Select::class, $element);
        $this->assertTrue($element->getDisabled());
    }

    public function testTypeElement()
    {
        $block = Bootstrap::getObjectManager()->get(
            Layout::class
        )->createBlock(
            Main::class
        );
        $block->setTemplate(null);
        $block->toHtml();
        $element = $block->getForm()->getElement('instance_code');
        $this->assertInstanceOf(Select::class, $element);
        $this->assertTrue($element->getDisabled());
    }
}
