<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Interception;

use Magento\Framework\App\AreaList;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Config\Scope;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Interception\Config\CacheManager;
use Magento\Framework\Interception\PluginList\PluginList;
use Magento\Framework\ObjectManager\Factory\Dynamic\Developer;
use Magento\Framework\ObjectManager\Relations\Runtime;
use Magento\Framework\ObjectManager\RelationsInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Serialize\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class GeneralTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractPlugin extends TestCase
{
    /**
     * Config reader
     *
     * @var MockObject
     */
    protected $_configReader;

    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Applicartion Object Manager
     *
     * @var ObjectManagerInterface
     */
    private $applicationObjectManager;

    /**
     * Set up Interception Config
     *
     * @param array $pluginConfig
     */
    public function setUpInterceptionConfig($pluginConfig)
    {
        $config = new \Magento\Framework\Interception\ObjectManager\Config\Developer();
        $factory = new Developer($config, null);

        $this->_configReader = $this->createMock(ReaderInterface::class);
        $this->_configReader->expects(
            $this->any()
        )->method(
            'read'
        )->willReturn(
            $pluginConfig
        );

        $areaList = $this->createMock(AreaList::class);
        $areaList->expects($this->any())->method('getCodes')->willReturn([]);
        $configScope = new Scope($areaList, 'global');
        $cache = $this->createMock(CacheInterface::class);
        $cacheManager = $this->createMock(CacheManager::class);
        $cacheManager->method('load')->willReturn(null);
        $definitions = new \Magento\Framework\ObjectManager\Definition\Runtime();
        $relations = new Runtime();
        $configLoader = $this->createMock(ConfigLoaderInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $directoryList = $this->createMock(DirectoryList::class);
        $configWriter = $this->createMock(PluginListGenerator::class);
        $interceptionConfig = new Config\Config(
            $this->_configReader,
            $configScope,
            $cache,
            $relations,
            $config,
            $definitions,
            'interception',
            null,
            $cacheManager
        );
        $interceptionDefinitions = new Definition\Runtime();
        $json = new Json();
        $sharedInstances = [
            CacheInterface::class => $cache,
            ScopeInterface::class => $configScope,
            ReaderInterface::class => $this->_configReader,
            RelationsInterface::class => $relations,
            \Magento\Framework\ObjectManager\ConfigInterface::class => $config,
            \Magento\Framework\Interception\ObjectManager\ConfigInterface::class => $config,
            \Magento\Framework\ObjectManager\DefinitionInterface::class => $definitions,
            DefinitionInterface::class => $interceptionDefinitions,
            SerializerInterface::class => $json,
            ConfigLoaderInterface::class => $configLoader,
            LoggerInterface::class => $logger,
            DirectoryList::class => $directoryList,
            \Magento\Framework\App\ObjectManager\ConfigWriterInterface::class => $configWriter
        ];
        $this->_objectManager = new \Magento\Framework\ObjectManager\ObjectManager(
            $factory,
            $config,
            $sharedInstances
        );
        $factory->setObjectManager($this->_objectManager);

        $config->setInterceptionConfig($interceptionConfig);
        $config->extend(
            [
                'preferences' => [
                    PluginListInterface::class =>
                        PluginList::class,
                    ConfigWriterInterface::class =>
                        PluginListGenerator::class
                ],
            ]
        );
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        if (!$this->_objectManager) {
            return;
        }

        $this->applicationObjectManager = ObjectManager::getInstance();
        ObjectManager::setInstance($this->_objectManager);
    }

    /**
     * Tear down
     */
    protected function tearDown(): void
    {
        ObjectManager::setInstance($this->applicationObjectManager);
    }
}
