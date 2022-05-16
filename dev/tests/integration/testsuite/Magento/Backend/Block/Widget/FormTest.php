<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Widget;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Data\Form\Element\Date;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Layout;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Test class for \Magento\Backend\Block\Widget\Form
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testSetFieldset()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(
            DesignInterface::class
        )->setArea(
            FrontNameResolver::AREA_CODE
        )->setDefaultDesignTheme();
        $layout = $objectManager->create(Layout::class);
        $formBlock = $layout->addBlock(Form::class);
        $fieldSet = $objectManager->create(Fieldset::class);
        $arguments = [
            'data' => [
                'attribute_code' => 'date',
                'backend_type' => 'datetime',
                'frontend_input' => 'date',
                'frontend_label' => 'Date',
            ],
        ];
        $attributes = [$objectManager->create(Attribute::class, $arguments)];
        $method = new ReflectionMethod(Form::class, '_setFieldset');
        $method->setAccessible(true);
        $method->invoke($formBlock, $attributes, $fieldSet);
        $fields = $fieldSet->getElements();

        $this->assertCount(1, $fields);
        $this->assertInstanceOf(Date::class, $fields[0]);
        $this->assertNotEmpty($fields[0]->getDateFormat());
    }
}
