<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\Indexer\Category\Product\Action;

use Magento\Catalog\Model\Indexer\Category\Product\Action\Full as OriginObject;
use Magento\Framework\Interception\PluginListInterface;
use Magento\TestFramework\Catalog\Model\Indexer\Category\Product\Action\Full as PreferenceObject;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\Catalog\Model\Indexer\Category\Products\Action\Full *
 */
class FullTest extends TestCase
{
    /**
     * @var PreferenceObject
     */
    private $interceptor;

    /**
     * List of plugins
     *
     * @var PluginListInterface
     */
    private $pluginList;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Test possibility to add object preference
     */
    public function testPreference()
    {
        $interceptorClassName = get_class($this->interceptor);

        // Check interceptor class name
        $this->assertEquals($interceptorClassName, PreferenceObject::class . '\Interceptor');

        //check that there are no fatal errors
        $this->pluginList->getNext($interceptorClassName, 'execute');
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $preferenceObject = $this->objectManager->get(PreferenceObject::class);
        $this->objectManager->addSharedInstance($preferenceObject, OriginObject::class);
        $this->interceptor = $this->objectManager->get(OriginObject::class);
        $this->pluginList = $this->objectManager->get(PluginListInterface::class);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $this->objectManager->removeSharedInstance(OriginObject::class);
    }
}
