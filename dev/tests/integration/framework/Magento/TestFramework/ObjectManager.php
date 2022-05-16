<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework;

use Magento\Framework\App\Config\Base;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Config\Scope;
use Magento\Framework\ObjectManager\Config\Mapper\Dom;
use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\Framework\ObjectManager\DefinitionInterface;
use Magento\Framework\ObjectManager\Factory\Factory;
use Magento\Framework\ObjectManager\FactoryInterface;
use Magento\Framework\ObjectManager\RelationsInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use ReflectionClass;

/**
 * ObjectManager for integration test framework.
 */
class ObjectManager extends \Magento\Framework\App\ObjectManager
{
    /**
     * Classes with xml properties to explicitly call __destruct() due to https://bugs.php.net/bug.php?id=62468
     *
     * @var array
     */
    protected $_classesToDestruct = [
        Layout::class,
        Registry::class
    ];

    /**
     * @var array
     */
    protected $persistedInstances = [
        ResourceConnection::class,
        Scope::class,
        RelationsInterface::class,
        ConfigInterface::class,
        \Magento\Framework\Interception\DefinitionInterface::class,
        DefinitionInterface::class,
        \Magento\Framework\Session\Config::class,
        Dom::class,
    ];

    /**
     * Set objectManager.
     *
     * @param ObjectManagerInterface $objectManager
     * @return ObjectManagerInterface
     */
    public static function setInstance(ObjectManagerInterface $objectManager)
    {
        return self::$_instance = $objectManager;
    }

    /**
     * Clear InstanceManager cache.
     *
     * @return ObjectManager
     */
    public function clearCache()
    {
        foreach ($this->_classesToDestruct as $className) {
            if (isset($this->_sharedInstances[$className])) {
                $this->_sharedInstances[$className] = null;
            }
        }

        Base::destroy();
        $sharedInstances = [
            ObjectManagerInterface::class => $this,
            \Magento\Framework\App\ObjectManager::class => $this,
        ];
        foreach ($this->persistedInstances as $persistedClass) {
            if (isset($this->_sharedInstances[$persistedClass])) {
                $sharedInstances[$persistedClass] = $this->_sharedInstances[$persistedClass];
            }
        }
        $this->_sharedInstances = $sharedInstances;
        $this->_config->clean();
        $this->clearMappedTableNames();

        return $this;
    }

    /**
     * Clear mapped table names list.
     *
     * @return void
     */
    private function clearMappedTableNames()
    {
        $resourceConnection = $this->get(ResourceConnection::class);
        if ($resourceConnection) {
            $reflection = new ReflectionClass($resourceConnection);
            $dataProperty = $reflection->getProperty('mappedTableNames');
            $dataProperty->setAccessible(true);
            $dataProperty->setValue($resourceConnection, null);
        }
    }

    /**
     * Add shared instance.
     *
     * @param mixed $instance
     * @param string $className
     * @param bool $forPreference Resolve preference for class
     * @return void
     */
    public function addSharedInstance($instance, $className, $forPreference = false)
    {
        $className = $forPreference ? $this->_config->getPreference($className) : $className;
        $this->_sharedInstances[$className] = $instance;
    }

    /**
     * Remove shared instance.
     *
     * @param string $className
     * @param bool $forPreference Resolve preference for class
     * @return void
     */
    public function removeSharedInstance($className, $forPreference = false)
    {
        $className = $forPreference ? $this->_config->getPreference($className) : $className;
        unset($this->_sharedInstances[$className]);
    }

    /**
     * Get object factory
     *
     * @return FactoryInterface|Factory
     */
    public function getFactory()
    {
        return $this->_factory;
    }
}
