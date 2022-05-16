<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Test\Unit\View\Element;

use Magento\Framework\Config\DataInterface;
use Magento\Framework\Config\DataInterfaceFactory;
use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\View\Element\UiComponent\ContextFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Sanitizer;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Config\Reader\Definition\Data;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UiComponentFactoryTest extends TestCase
{
    /** @var UiComponentFactory */
    protected $model;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var ObjectManagerInterface|MockObject */
    protected $objectManagerMock;

    /** @var InterpreterInterface|MockObject */
    protected $interpreterMock;

    /** @var ContextFactory|MockObject */
    protected $contextFactoryMock;

    /** @var DataInterfaceFactory|MockObject */
    protected $dataInterfaceFactoryMock;

    /** @var Data|MockObject */
    protected $dataMock;

    public function testCreateRootComponent()
    {
        $identifier = "product_listing";
        $context = $this->createMock(ContextInterface::class);
        $bundleComponents = [
            'attributes' => [
                'class' => 'Some\Class\Component',
            ],
            'arguments' => [
                'config' => [
                    'class' => 'Some\Class\Component2'
                ]
            ],
            'children' => []
        ];
        $uiConfigMock = $this->createMock(DataInterface::class);
        $this->dataInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($uiConfigMock);
        $uiConfigMock->expects($this->once())
            ->method('get')
            ->willReturn($bundleComponents);

        $this->contextFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($context);
        $expectedArguments = [
            'config' => [
                'class' => 'Some\Class\Component2'
            ],
            'data' => [
                'name' => $identifier
            ],
            'context' => $context,
            'components' => []
        ];
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Some\Class\Component2', $expectedArguments);
        $this->model->create($identifier);
    }

    public function testNonRootComponent()
    {
        $identifier = "custom_select";
        $name = "fieldset";
        $context = $this->createMock(ContextInterface::class);
        $arguments = ['context' => $context];
        $definitionArguments = [
            'componentType' => 'select',
            'attributes' => [
                'class' => '\Some\Class',
            ],
            'arguments' => []
        ];
        $expectedArguments = [
            'data' => [
                'name' => $identifier
            ],
            'context' => $context,
            'components' => []
        ];
        $this->dataMock->expects($this->once())
            ->method('get')
            ->with($name)
            ->willReturn($definitionArguments);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('\Some\Class', $expectedArguments);
        $this->model->create($identifier, $name, $arguments);
    }

    protected function setUp(): void
    {
        $this->objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)
            ->getMockForAbstractClass();
        $this->interpreterMock = $this->getMockBuilder(InterpreterInterface::class)
            ->getMockForAbstractClass();
        $this->contextFactoryMock = $this
            ->getMockBuilder(ContextFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataInterfaceFactoryMock = $this->getMockBuilder(DataInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataMock = $this->createMock(DataInterface::class);
        $sanitizerMock = $this->createMock(Sanitizer::class);
        $sanitizerMock->method('sanitize')->willReturnArgument(0);
        $sanitizerMock->method('sanitizeComponentMetadata')->willReturnArgument(0);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            UiComponentFactory::class,
            [
                'objectManager' => $this->objectManagerMock,
                'argumentInterpreter' => $this->interpreterMock,
                'contextFactory' => $this->contextFactoryMock,
                'configFactory' => $this->dataInterfaceFactoryMock,
                'data' => [],
                'componentChildFactories' => [],
                'definitionData' => $this->dataMock,
                'sanitizer' => $sanitizerMock
            ]
        );
    }
}
