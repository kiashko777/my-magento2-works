<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Interception;

use Magento\Framework\Interception\Fixture\Intercepted;
use Magento\Framework\Interception\Fixture\Intercepted\InterfacePlugin;
use Magento\Framework\Interception\Fixture\Intercepted\Plugin;
use Magento\Framework\Interception\Fixture\InterceptedInterface;

/**
 * Class GeneralTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GeneralTest extends AbstractPlugin
{
    public function testMethodCanBePluginized()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<P:D>1: <D>test</D></P:D>', $subject->D('test'));
    }

    public function testPluginCanCallOnlyNextMethodOnNext()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals(
            '<IP:aG><P:aG><G><P:G><P:bG><IP:G><IP:bG>test</IP:bG></IP:G></P:bG></P:G></G></P:aG></IP:aG>',
            $subject->G('test')
        );
    }

    public function testBeforeAndAfterPluginsAreExecuted()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals(
            '<IP:F><P:D>1: <D>prefix_<F><IP:C><P:C><C>test</C></P:C>' . '</IP:C></F></D></P:D></IP:F>',
            $subject->A('prefix_')->F('test')
        );
    }

    public function testPluginCallsOtherMethodsOnSubject()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals(
            '<P:K><IP:F><P:D>1: <D>prefix_<F><IP:C><P:C><C><IP:C><P:C><C>test' .
            '</C></P:C></IP:C></C></P:C></IP:C></F></D></P:D></IP:F></P:K>',
            $subject->A('prefix_')->K('test')
        );
    }

    public function testInterfacePluginsAreInherited()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<IP:C><P:C><C>test</C></P:C></IP:C>', $subject->C('test'));
    }

    public function testInternalMethodCallsAreIntercepted()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<B>12<IP:C><P:C><C>1</C></P:C></IP:C></B>', $subject->B('1', '2'));
    }

    public function testChainedMethodsAreIntercepted()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<P:D>1: <D>prefix_test</D></P:D>', $subject->A('prefix_')->D('test'));
    }

    public function testFinalMethodWorks()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<P:D>1: <D>prefix_test</D></P:D>', $subject->A('prefix_')->D('test'));
        $this->assertEquals('<E>prefix_final</E>', $subject->E('final'));
        $this->assertEquals('<P:D>2: <D>prefix_test</D></P:D>', $subject->D('test'));
    }

    public function testObjectKeepsStateBetweenInvocations()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<P:D>1: <D>test</D></P:D>', $subject->D('test'));
        $this->assertEquals('<P:D>2: <D>test</D></P:D>', $subject->D('test'));
    }

    protected function setUp(): void
    {
        $this->setUpInterceptionConfig(
            [InterceptedInterface::class =>
                [
                    'plugins' => [
                        'first' => [
                            'instance' =>
                                InterfacePlugin::class,
                            'sortOrder' => 10,
                        ],
                    ],
                ], Intercepted::class =>
                [
                    'plugins' => [
                        'second' => [
                            'instance' => Plugin::class,
                            'sortOrder' => 20,
                        ],
                    ],
                ],
            ]
        );

        parent::setUp();
    }
}
