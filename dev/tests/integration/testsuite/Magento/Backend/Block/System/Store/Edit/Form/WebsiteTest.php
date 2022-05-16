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
class WebsiteTest extends TestCase
{
    /**
     * @var Website
     */
    protected $_block;

    public function testPrepareForm()
    {
        $form = $this->_block->getForm();
        $this->assertEquals('website_fieldset', $form->getElement('website_fieldset')->getId());
        $this->assertEquals('website_name', $form->getElement('website_name')->getId());
        $this->assertEquals('website', $form->getElement('store_type')->getValue());
    }

    protected function setUp(): void
    {
        parent::setUp();

        /** @var $objectManager ObjectManager */
        $objectManager = Bootstrap::getObjectManager();
        $registryData = [
            'store_type' => 'website',
            'store_data' => $objectManager->create(\Magento\Store\Model\Website::class),
            'store_action' => 'add',
        ];
        foreach ($registryData as $key => $value) {
            $objectManager->get(Registry::class)->register($key, $value);
        }

        /** @var $layout Layout */
        $layout = $objectManager->get(LayoutInterface::class);

        $this->_block = $layout->createBlock(Website::class);

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
