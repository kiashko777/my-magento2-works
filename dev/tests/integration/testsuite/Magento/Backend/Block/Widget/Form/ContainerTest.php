<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Widget\Form;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Block\User\Edit\Form;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class ContainerTest extends TestCase
{
    public function testGetFormHtml()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        // Create block with blocking _prepateLayout(), which is used by block to instantly add 'form' child
        /** @var $block Container */
        $block = $this->getMockBuilder(Container::class)
            ->setMethods(['_prepareLayout'])
            ->setConstructorArgs([$objectManager->create(Context::class)])
            ->getMock();

        $layout->addBlock($block, 'block');
        $form = $layout->addBlock(Text::class, 'form', 'block');

        $expectedHtml = '<b>html</b>';
        $this->assertNotEquals($expectedHtml, $block->getFormHtml());
        $form->setText($expectedHtml);
        $this->assertEquals($expectedHtml, $block->getFormHtml());
    }

    public function testPseudoConstruct()
    {
        /** @var $block Container */
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Container::class,
            '',
            [
                'data' => [
                    \Magento\Backend\Block\Widget\Container::PARAM_CONTROLLER => 'user',
                    Container::PARAM_MODE => 'edit',
                    Container::PARAM_BLOCK_GROUP => 'Magento_User'
                ]
            ]
        );
        $this->assertInstanceOf(Form::class, $block->getChildBlock('form'));
    }
}
