<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magento\Integration\Block\Adminhtml\Integration\Activate\Permissions\Tab;

use Magento\Framework\Registry;
use Magento\Integration\Controller\Adminhtml\Integration as IntegrationController;
use Magento\Integration\Model\Integration;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDataFixture Magento/Integration/_files/integration_all_permissions.php
 */
class WebapiTest extends TestCase
{
    /** @var Registry */
    protected $registry;

    public function testGetSelectedResourcesJsonEmpty()
    {
        $expectedResult = '[]';
        $this->assertEquals($expectedResult, $this->createApiTabBlock()->getSelectedResourcesJson());
    }

    /**
     * @return Webapi
     */
    protected function createApiTabBlock()
    {
        $objectManager = Bootstrap::getObjectManager();
        return $objectManager->create(
            Webapi::class
        );
    }

    public function testGetSelectedResourcesJson()
    {
        $expectedResult = '["Magento_Backend::dashboard",';
        $this->registry->register(
            IntegrationController::REGISTRY_KEY_CURRENT_INTEGRATION,
            $this->getFixtureIntegration()->getData()
        );
        $this->assertStringContainsString($expectedResult, $this->createApiTabBlock()->getSelectedResourcesJson());
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

    public function testGetResourcesTreeJson()
    {
        $expectedResult = '[{"attr":{"data-id":"Magento_Backend::dashboard"},"data":"Dashboard","children":[],"state":';
        $this->registry->register(
            IntegrationController::REGISTRY_KEY_CURRENT_INTEGRATION,
            $this->getFixtureIntegration()->getData()
        );
        $this->assertStringContainsString($expectedResult, $this->createApiTabBlock()->getResourcesTreeJson());
    }

    public function testCanShowTabNegative()
    {
        $this->assertFalse($this->createApiTabBlock()->canShowTab());
    }

    public function testCanShowTabPositive()
    {
        $integrationData = $this->getFixtureIntegration()->getData();
        $integrationData[Integration::SETUP_TYPE] = Integration::TYPE_CONFIG;
        $this->registry->register(IntegrationController::REGISTRY_KEY_CURRENT_INTEGRATION, $integrationData);
        $this->assertTrue($this->createApiTabBlock()->canShowTab());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        $this->registry = $objectManager->get(Registry::class);
    }

    protected function tearDown(): void
    {
        $this->registry->unregister(IntegrationController::REGISTRY_KEY_CURRENT_INTEGRATION);
        parent::tearDown();
    }
}
