<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Widget;

use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main;
use Magento\Widget\Model\Widget\Instance;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class TabsTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testAddTab()
    {
        /** @var $widgetInstance Instance */
        $widgetInstance = Bootstrap::getObjectManager()->create(
            Instance::class
        );
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->register('current_widget_instance', $widgetInstance);

        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Tabs */
        $block = $layout->createBlock(Tabs::class, 'block');
        $layout->addBlock(Main::class, 'child_tab', 'block');
        $block->addTab('tab_id', 'child_tab');

        $this->assertEquals(['tab_id'], $block->getTabsIds());
    }
}
