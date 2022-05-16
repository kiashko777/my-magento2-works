<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Block\Adminhtml\Widget\Instance;

use Magento\Catalog\Block\Product\Widget\NewWidget;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\Widget\Model\Widget\Instance;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class EditTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testConstruct()
    {
        $type = NewWidget::class;
        $code = 'catalog_product_newwidget';
        $theme = Bootstrap::getObjectManager()->get(
            DesignInterface::class
        )->setDefaultDesignTheme()->getDesignTheme();

        /** @var $widgetInstance Instance */
        $widgetInstance = Bootstrap::getObjectManager()->create(
            Instance::class
        );
        $widgetInstance->setType($type)->setCode($code)->setThemeId($theme->getId())->save();
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->register('current_widget_instance', $widgetInstance);

        Bootstrap::getObjectManager()->get(
            RequestInterface::class
        )->setParam(
            'instance_id',
            $widgetInstance->getId()
        );
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Edit::class,
            'widget'
        );
        $this->assertArrayHasKey('widget-delete_button', $block->getLayout()->getAllBlocks());
    }
}
