<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Reports\Block\Adminhtml\Filter;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Layout;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Test class for \Magento\Reports\Block\Adminhtml\Filter\Form
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Bootstrap::getObjectManager()->get(
            DesignInterface::class
        )->setArea(
            FrontNameResolver::AREA_CODE
        )->setDefaultDesignTheme();
        $layout = Bootstrap::getObjectManager()->create(
            Layout::class
        );
        $block = $layout->addBlock(Form::class);
        $prepareFormMethod = new ReflectionMethod(Form::class, '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (['from', 'to'] as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
