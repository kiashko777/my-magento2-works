<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Integration\Model;

use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Integration\Model\ConfigBasedIntegrationManager.php.
 */
class ConfigBasedIntegrationManagerTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $consolidatedMock;

    /**
     * @var ConfigBasedIntegrationManager
     */
    protected $integrationManager;

    /**
     * @var IntegrationServiceInterface
     */
    protected $integrationService;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @magentoDbIsolation enabled
     */
    public function testProcessConfigBasedIntegrations()
    {
        $newIntegrations = require __DIR__ . '/Config/Consolidated/_files/integration.php';
        $this->consolidatedMock
            ->expects($this->any())
            ->method('getIntegrations')
            ->willReturn($newIntegrations);

        // Check that the integrations do not exist already
        foreach ($newIntegrations as $integrationName => $integrationData) {
            $integration = $this->integrationService->findByName($integrationName);
            $this->assertNull($integration->getId(), 'Integration already exists');
        }

        // Create new integrations
        $this->assertEquals(
            $newIntegrations,
            $this->integrationManager->processConfigBasedIntegrations($newIntegrations),
            'Error processing config based integrations.'
        );
        $createdIntegrations = [];

        // Check that the integrations are new with "inactive" status
        foreach ($newIntegrations as $integrationName => $integrationData) {
            $integration = $this->integrationService->findByName($integrationName);
            $this->assertNotEmpty($integration->getId(), 'Integration was not created');
            $this->assertEquals(
                $integration::STATUS_INACTIVE,
                $integration->getStatus(),
                'Integration is not created with "inactive" status'
            );
            $createdIntegrations[$integrationName] = $integration;
        }

        // Rerun integration creation with the same data (data has not changed)
        $this->assertEquals(
            $newIntegrations,
            $this->integrationManager->processConfigBasedIntegrations($newIntegrations),
            'Error processing config based integrations.'
        );

        // Check that the integrations are not recreated when data has not actually changed
        foreach ($newIntegrations as $integrationName => $integrationData) {
            $integration = $this->integrationService->findByName($integrationName);
            $this->assertEquals(
                $createdIntegrations[$integrationName]->getId(),
                $integration->getId(),
                'Integration ID has changed'
            );
            $this->assertEquals(
                $createdIntegrations[$integrationName]->getStatus(),
                $integration->getStatus(),
                'Integration status has changed'
            );
        }
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->consolidatedMock = $this->createMock(ConsolidatedConfig::class);
        $this->objectManager->addSharedInstance(
            $this->consolidatedMock,
            ConsolidatedConfig::class
        );
        $this->integrationManager = $this->objectManager->create(
            ConfigBasedIntegrationManager::class,
            []
        );
        $this->integrationService = $this->objectManager->create(
            IntegrationServiceInterface::class,
            []
        );
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->objectManager->removeSharedInstance(ConsolidatedConfig::class);
        parent::tearDown();
    }
}
