<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $integration Integration */

use Magento\Integration\Api\AuthorizationServiceInterface;
use Magento\Integration\Model\Integration;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$integration = $objectManager->create(Integration::class);
$integration->setName('Fixture Integration')->save();

/** Grant permissions to integrations */
/** @var AuthorizationServiceInterface */
$authorizationService = $objectManager->create(AuthorizationServiceInterface::class);
$authorizationService->grantAllPermissions($integration->getId());
