<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\System\Store\Edit\Form;

use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea Adminhtml
 */
class StoreTest extends TestCase
{
    /**
     * @var Store
     */
    protected $_block;

    public function testPrepareForm()
    {
        $form = $this->_block->getForm();
        $this->assertEquals('store_fieldset', $form->getElement('store_fieldset')->getId());
        $this->assertEquals('store_name', $form->getElement('store_name')->getId());
        $this->assertEquals('store', $form->getElement('store_type')->getValue());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $registryData = [
            'store_type' => 'store',
            'store_data' => Bootstrap::getObjectManager()->create(
                \Magento\Store\Model\Store::class
            ),
            'store_action' => 'add',
        ];
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        foreach ($registryData as $key => $value) {
            $objectManager->get(Registry::class)->register($key, $value);
        }

        /** @var $layout Layout */
        $layout = $objectManager->get(LayoutInterface::class);

        $this->_block = $layout->createBlock(Store::class);

        $this->_block->toHtml();
    }

    protected function tearDown(): void
    {
        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(Registry::class)->unregister('store_type');
        $objectManager->get(Registry::class)->unregister('store_data');
        $objectManager->get(Registry::class)->unregister('store_action');
    }
}
