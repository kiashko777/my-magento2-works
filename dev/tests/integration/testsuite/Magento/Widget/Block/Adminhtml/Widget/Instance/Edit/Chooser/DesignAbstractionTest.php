<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\State;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Layout\ProcessorFactory;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class DesignAbstractionTest extends TestCase
{
    /**
     * @var DesignAbstraction|
     *      \PHPUnit\Framework\MockObject\MockObject
     */
    protected $_block;

    public function testToHtml()
    {
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/_files/design-abstraction_select.html',
            $this->_block->toHtml()
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = Bootstrap::getObjectManager();
        $layoutUtility = new \Magento\Framework\View\Utility\Layout($this);
        $appState = $objectManager->get(State::class);
        $appState->setAreaCode(FrontNameResolver::AREA_CODE);
        $processorMock = $this->getMockBuilder(ProcessorInterface::class)
            ->setMethods(['isPageLayoutDesignAbstraction'])
            ->getMockForAbstractClass();
        $processorMock->expects($this->exactly(2))->method('isPageLayoutDesignAbstraction')->willReturnCallback(

            function ($abstraction) {
                return $abstraction['design_abstraction'] === 'page_layout';
            }

        );
        $processorFactoryMock =
            $this->createPartialMock(ProcessorFactory::class, ['create']);
        $processorFactoryMock->expects($this->exactly(2))->method('create')->willReturnCallback(

            function ($data) use ($processorMock, $layoutUtility) {
                return $data === [] ? $processorMock : $layoutUtility->getLayoutUpdateFromFixture(
                    glob(__DIR__ . '/_files/layout/*.xml')
                );
            }

        );

        $this->_block = new DesignAbstraction(
            $objectManager->get(Context::class),
            $processorFactoryMock,
            $objectManager->get(CollectionFactory::class),
            $appState,
            [
                'name' => 'design_abstractions',
                'id' => 'design_abstraction_select',
                'class' => 'design-abstraction-select',
                'title' => 'Design Abstraction Select'
            ]
        );
    }
}
