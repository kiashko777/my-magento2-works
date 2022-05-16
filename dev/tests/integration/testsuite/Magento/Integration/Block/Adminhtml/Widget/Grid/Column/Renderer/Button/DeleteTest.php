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
class DeleteTest extends TestCase
{
    /**
     * @var Delete
     */
    protected $deleteButtonBlock;

    public function testRender()
    {
        $integration = $this->getFixtureIntegration();
        $buttonHtml = $this->deleteButtonBlock->render($integration);
        self::assertStringContainsString('title="Remove"', $buttonHtml);
        self::assertStringContainsString(
            'this.setAttribute(\'data-url\', '
            . '\'http://localhost/index.php/backend/admin/integration/delete/id/'
            . $integration->getId(),
            $buttonHtml
        );
        $this->assertStringNotContainsString('disabled', $buttonHtml);
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

    public function testRenderDisabled()
    {
        $integration = $this->getFixtureIntegration();
        $integration->setSetupType(Integration::TYPE_CONFIG);
        $buttonHtml = $this->deleteButtonBlock->render($integration);
        self::assertStringContainsString(
            'title="' . $this->deleteButtonBlock->escapeHtmlAttr('Uninstall the extension to remove this integration')
            . '"',
            $buttonHtml
        );
        self::assertStringContainsString(
            'this.setAttribute(\'data-url\', '
            . '\'http://localhost/index.php/backend/admin/integration/delete/id/'
            . $integration->getId(),
            $buttonHtml
        );
        self::assertStringContainsString('disabled="disabled"', $buttonHtml);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        /** @var Http $request */
        $request = $objectManager->get(Http::class);
        $request->setRouteName('Adminhtml')->setControllerName('integration');
        $this->deleteButtonBlock = $objectManager->create(
            Delete::class
        );
        $column = $objectManager->create(Column::class);
        $this->deleteButtonBlock->setColumn($column);
    }
}
