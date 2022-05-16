<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Newsletter\Block\Adminhtml\Queue\Edit;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\DesignInterface;
use Magento\Newsletter\Model\Queue;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Test class for \Magento\Newsletter\Block\Adminhtml\Queue\Edit\Form
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = Bootstrap::getObjectManager();
        $queue = $objectManager->get(Queue::class);
        /** @var Registry $registry */
        $registry = $objectManager->get(Registry::class);
        $registry->register('current_queue', $queue);

        $objectManager->get(
            DesignInterface::class
        )->setArea(
            FrontNameResolver::AREA_CODE
        )->setDefaultDesignTheme();
        $objectManager->get(
            ScopeInterface::class
        )->setCurrentScope(
            FrontNameResolver::AREA_CODE
        );
        $block = $objectManager->create(
            Form::class,
            ['registry' => $registry]
        );
        $prepareFormMethod = new ReflectionMethod(
            Form::class,
            '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);

        $statuses = [
            Queue::STATUS_NEVER,
            Queue::STATUS_PAUSE,
        ];
        foreach ($statuses as $status) {
            $queue->setQueueStatus($status);
            $prepareFormMethod->invoke($block);
            $element = $block->getForm()->getElement('date');
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getTimeFormat());
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
