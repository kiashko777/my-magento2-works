<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Integration\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Authorization;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Webapi\Model\WebapiRoleLocator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Integration authorization service test.
 */
class AuthorizationServiceTest extends TestCase
{
    /** @var AuthorizationService */
    protected $_service;

    /** @var Authorization */
    protected $libAuthorization;

    /** @var UserContextInterface|MockObject */
    protected $userContextMock;

    /**
     * @magentoDbIsolation enabled
     */
    public function testGrantPermissions()
    {
        $integrationId = rand(1, 1000);
        $resources = ['Magento_Sales::create', 'Magento_Cms::page', 'Magento_Backend::dashboard'];
        /** Preconditions check */
        $this->_ensurePermissionsAreNotGranted($integrationId, $resources);

        $this->_service->grantPermissions($integrationId, $resources);

        /** Validate that access to the specified resources is granted */
        $this->_ensurePermissionsAreGranted($integrationId, $resources);
    }

    /**
     * Check if access to the specified resources is prohibited to the user.
     *
     * @param int $integrationId
     * @param string[] $resources
     */
    protected function _ensurePermissionsAreNotGranted($integrationId, $resources)
    {
        $this->userContextMock
            ->expects($this->any())
            ->method('getUserId')
            ->willReturn($integrationId);
        foreach ($resources as $resource) {
            $this->assertFalse(
                $this->libAuthorization->isAllowed($resource),
                "Access to resource '{$resource}' is expected to be prohibited."
            );
        }
    }

    /**
     * Check if user has access to the specified resources.
     *
     * @param int $integrationId
     * @param string[] $resources
     */
    protected function _ensurePermissionsAreGranted($integrationId, $resources)
    {
        $this->userContextMock
            ->expects($this->any())
            ->method('getUserId')
            ->willReturn($integrationId);
        foreach ($resources as $resource) {
            $this->assertTrue(
                $this->libAuthorization->isAllowed($resource),
                "Access to resource '{$resource}' is prohibited while it is expected to be granted."
            );
        }
    }

    /**
     * @param int $integrationId
     * @param string[] $initialResources
     * @param string[] $newResources
     * @magentoDbIsolation enabled
     * @dataProvider changePermissionsProvider
     */
    public function testChangePermissions($integrationId, $initialResources, $newResources)
    {
        $this->_service->grantPermissions($integrationId, $initialResources);
        /** Preconditions check */
        $this->_ensurePermissionsAreGranted($integrationId, $initialResources);
        $this->_ensurePermissionsAreNotGranted($integrationId, $newResources);

        $this->_service->grantPermissions($integrationId, $newResources);

        /** Check the results of permissions change */
        $this->_ensurePermissionsAreGranted($integrationId, $newResources);
        $this->_ensurePermissionsAreNotGranted($integrationId, $initialResources);
    }

    public function changePermissionsProvider()
    {
        return [
            'integration' => [
                'integrationId' => rand(1, 1000),
                'initialResources' => ['Magento_Cms::page', 'Magento_Backend::dashboard'],
                'newResources' => ['Magento_Sales::cancel', 'Magento_Cms::page_delete'],
            ],
            'integration clear permissions' => [
                'integrationId' => rand(1, 1000),
                'initialResources' => ['Magento_Sales::capture', 'Magento_Cms::page_delete'],
                'newResources' => [],
            ]
        ];
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testGrantAllPermissions()
    {
        $integrationId = rand(1, 1000);
        $this->_service->grantAllPermissions($integrationId);
        $this->_ensurePermissionsAreGranted($integrationId, ['Magento_Backend::all']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();
        $loggerMock->expects($this->any())->method('critical')->willReturnSelf();
        $this->_service = $objectManager->create(
            AuthorizationService::class,
            [
                'logger' => $loggerMock
            ]
        );

        $this->userContextMock = $this->getMockForAbstractClass(
            UserContextInterface::class
        );
        $this->userContextMock
            ->expects($this->any())
            ->method('getUserType')
            ->willReturn(UserContextInterface::USER_TYPE_INTEGRATION);
        $roleLocator = $objectManager->create(
            WebapiRoleLocator::class,
            ['userContext' => $this->userContextMock]
        );
        $this->libAuthorization = $objectManager->create(
            Authorization::class,
            ['roleLocator' => $roleLocator]
        );
    }
}
