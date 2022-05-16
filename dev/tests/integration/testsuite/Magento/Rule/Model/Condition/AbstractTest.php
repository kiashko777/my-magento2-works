<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Rule\Model\Condition\AbstractCondition
 */

namespace Magento\Rule\Model\Condition;

use Magento\Framework\Data\Form;
use Magento\Framework\View\Layout;
use Magento\Rule\Block\Editable;
use Magento\Rule\Model\AbstractModel;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class AbstractTest extends TestCase
{
    public function testGetValueElement()
    {
        $layoutMock = $this->createMock(Layout::class);

        $objectManager = Bootstrap::getObjectManager();
        $context = $objectManager->create(Context::class, ['layout' => $layoutMock]);

        /** @var AbstractCondition $model */
        $model = $this->getMockForAbstractClass(
            AbstractCondition::class,
            [$context],
            '',
            true,
            true,
            true,
            ['getValueElementRenderer']
        );
        $editableBlock = Bootstrap::getObjectManager()->create(
            Editable::class
        );
        $model->expects($this->any())->method('getValueElementRenderer')->willReturn($editableBlock);

        $rule = $this->getMockBuilder(AbstractModel::class)
            ->setMethods(['getForm'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $rule->expects($this->any())
            ->method('getForm')
            ->willReturn(
                Bootstrap::getObjectManager()->create(Form::class)
            );
        $model->setRule($rule);

        $property = new ReflectionProperty(AbstractCondition::class, '_inputType');
        $property->setAccessible(true);
        $property->setValue($model, 'date');

        $element = $model->getValueElement();
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
