<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Button;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Framework\App\Request\Http;
use Magento\Integration\Model\Integration;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 * @magentoDataFixture Magento/Integration/_files/integration_all_permissions.php
 */
class EditTest extends TestCase
{
    /**
     * @var Edit
     */
    protected $editButtonBlock;

    public function testRenderEdit()
    {
        $integration = $this->getFixtureIntegration();
        $buttonHtml = $this->editButtonBlock->render($integration);
        $this->assertStringContainsString('title="Edit"', $buttonHtml);
        $this->assertStringContainsString('class="' . $this->editButtonBlock->escapeHtmlAttr('action edit') . '"', $buttonHtml);
        $this->assertStringContainsString(
            'window.location.href=\'http://localhost/index.php/backend/admin/integration/edit/id/'
            . $integration->getId(),
            $buttonHtml
        );
    }

    /**
     * @return Integration
     */
    protected function getFixtureIntegration()
    {
        /** @var $integration Integration */
        $objectManager = Bootstrap::getObjectManager();
        $integration = $objectManager->create(Integration::class);
        return $integration->load('Fixture Integration', 'name');
    }

    public function testRenderView()
    {
        $integration = $this->getFixtureIntegration();
        $integration->setSetupType(Integration::TYPE_CONFIG);
        $buttonHtml = $this->editButtonBlock->render($integration);
        $this->assertStringContainsString('title="View"', $buttonHtml);
        $this->assertStringContainsString('class="' . $this->editButtonBlock->escapeHtmlAttr('action info') . '"', $buttonHtml);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        /** @var Http $request */
        $request = $objectManager->get(Http::class);
        $request->setRouteName('Adminhtml')->setControllerName('integration');
        $this->editButtonBlock = $objectManager->create(
            Edit::class
        );
        $column = $objectManager->create(Column::class);
        $this->editButtonBlock->setColumn($column);
    }
}
