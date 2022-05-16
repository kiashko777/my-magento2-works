<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Interception;

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Interception\ConfigLoaderInterface;
use Magento\Framework\Interception\PluginListGenerator;
use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\Framework\ObjectManager\DefinitionInterface;
use Magento\Framework\ObjectManager\RelationsInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Provides plugin list configuration
 */
class PluginList extends \Magento\Framework\Interception\PluginList\PluginList
{
    /**
     * @var array
     */
    protected $_originScopeScheme = [];

    /**
     * Constructor
     *
     * @param ReaderInterface $reader
     * @param ScopeInterface $configScope
     * @param CacheInterface $cache
     * @param RelationsInterface $relations
     * @param ConfigInterface $omConfig
     * @param \Magento\Framework\Interception\DefinitionInterface $definitions
     * @param ObjectManagerInterface $objectManager
     * @param DefinitionInterface $classDefinitions
     * @param array $scopePriorityScheme
     * @param string|null $cacheId
     * @param SerializerInterface|null $serializer
     * @param ConfigLoaderInterface|null $configLoader
     * @param PluginListGenerator|null $pluginListGenerator
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ReaderInterface            $reader,
        ScopeInterface             $configScope,
        CacheInterface             $cache,
        RelationsInterface  $relations,
        ConfigInterface     $omConfig,
        \Magento\Framework\Interception\DefinitionInterface  $definitions,
        ObjectManagerInterface            $objectManager,
        DefinitionInterface $classDefinitions,
        array                                                $scopePriorityScheme,
                                                             $cacheId = 'plugins',
        SerializerInterface                                  $serializer = null,
        ConfigLoaderInterface                                $configLoader = null,
        PluginListGenerator                                  $pluginListGenerator = null
    )
    {
        parent::__construct(
            $reader,
            $configScope,
            $cache,
            $relations,
            $omConfig,
            $definitions,
            $objectManager,
            $classDefinitions,
            $scopePriorityScheme,
            $cacheId,
            $serializer,
            $configLoader,
            $pluginListGenerator
        );
        $this->_originScopeScheme = $this->_scopePriorityScheme;
    }

    /**
     * Reset internal cache
     */
    public function reset()
    {
        $this->_scopePriorityScheme = $this->_originScopeScheme;
        $this->_data = [];
        $this->_loadedScopes = [];
    }
}
