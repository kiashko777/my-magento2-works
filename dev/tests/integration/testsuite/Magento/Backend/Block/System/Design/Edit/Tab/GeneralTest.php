<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\System\Design\Edit\Tab;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\DesignInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Test class for \Magento\Backend\Block\System\Design\Edit\Tab\General
 * @magentoAppArea Adminhtml
 */
class GeneralTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(
            \Magento\Framework\View\DesignInterface::class
        )->setArea(
            FrontNameResolver::AREA_CODE
        )->setDefaultDesignTheme();
        $objectManager->get(
            Registry::class
        )->register(
            'design',
            $objectManager->create(DesignInterface::class)
        );
        $layout = $objectManager->create(Layout::class);
        $block = $layout->addBlock(General::class);
        $prepareFormMethod = new ReflectionMethod(
            General::class,
            '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (['date_from', 'date_to'] as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
