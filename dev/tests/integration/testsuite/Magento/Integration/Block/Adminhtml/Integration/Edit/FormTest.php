<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Integration\Block\Adminhtml\Integration\Edit;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;
use Magento\Integration\Controller\Adminhtml\Integration;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Integration\Block\Adminhtml\Integration\Edit\Form
 */
class FormTest extends TestCase
{
    /**
     * @var Form
     */
    private $block;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @magentoAppArea Adminhtml
     */
    public function testToHtmlNoExistingIntegrationData()
    {
        $this->assertStringContainsString(
            '<form id="edit_form" action="" method="post">',
            $this->block->toHtml()
        );
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoAppIsolation enabled
     */
    public function testToHtmlWithIntegrationData()
    {
        /** @var Registry $coreRegistry */
        $coreRegistry = $this->objectManager->get(Registry::class);
        $coreRegistry->unregister(Integration::REGISTRY_KEY_CURRENT_INTEGRATION);
        $id = 'idValue';
        $integrationData = [
            Info::DATA_ID => $id,
        ];
        $coreRegistry->register(Integration::REGISTRY_KEY_CURRENT_INTEGRATION, $integrationData);

        $html = $this->block->toHtml();

        $this->assertMatchesRegularExpression(
            "/<input id=\"integration_id\" name=\"id\".*value=\"$id\".*type=\"hidden\".*>/",
            $html
        );
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        /** @var $layout Layout */
        $layout = $this->objectManager->create(LayoutInterface::class);
        $this->block = $layout->createBlock(Form::class);
    }
}
