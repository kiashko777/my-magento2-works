<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Annotation;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleResource;
use Magento\TestFramework\Application;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

/**
 * Handler for applying reinstallMagento annotation.
 */
class ReinstallInstance
{
    /**
     * @var Application
     */
    private $application;

    /**
     * Constructor
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function startTest()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $resourceConnection = $objectManager->create(ResourceConnection::class);
        $objectManager->removeSharedInstance(ResourceConnection::class);
        $objectManager->addSharedInstance($resourceConnection, ResourceConnection::class);
        $this->application->reinitialize();
    }

    /**
     * Handler for 'endTest' event.
     *
     * @throws LocalizedException
     */
    public function endTest()
    {
        $this->application->cleanup();
        $this->application->reinitialize();
        ModuleResource::flush();
    }
}
