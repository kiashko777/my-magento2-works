<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Interception;

use Magento\Framework\Interception\Fixture\Intercepted;
use Magento\Framework\Interception\Fixture\Intercepted\FirstPlugin;
use Magento\Framework\Interception\Fixture\Intercepted\Plugin;

/**
 * Class TwoPluginTest
 */
class TwoPluginTest extends AbstractPlugin
{
    public function testPluginBeforeWins()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<X><P:bX/></X>', $subject->X('test'));
    }

    public function testPluginAroundWins()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<F:Y>test<F:Y/>', $subject->Y('test'));
    }

    public function testPluginAfterWins()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<P:aZ/>', $subject->Z('test'));
    }

    public function testPluginBeforeAroundWins()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<F:V><F:bV/><F:V/>', $subject->V('test'));
    }

    public function testPluginBeforeAroundAfterWins()
    {
        $subject = $this->_objectManager->create(Intercepted::class);
        $this->assertEquals('<F:aW/>', $subject->W('test'));
    }

    protected function setUp(): void
    {
        $this->setUpInterceptionConfig(
            [Intercepted::class => [
                'plugins' => [
                    'first' => [
                        'instance' => FirstPlugin::class,
                        'sortOrder' => 10,
                    ], 'second' => [
                        'instance' => Plugin::class,
                        'sortOrder' => 20,
                    ]
                ],
            ]
            ]
        );

        parent::setUp();
    }
}
