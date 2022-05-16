<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Helper providing exclusive restricted access to the underlying bootstrap instance
 */

namespace Magento\TestFramework\Helper;

use Magento\Framework\App\AreaList;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

class Bootstrap
{
    /**
     * @var Bootstrap
     */
    private static $_instance;

    /**
     * @var ObjectManagerInterface
     */
    private static $_objectManager;

    /**
     * @var \Magento\TestFramework\Bootstrap
     */
    protected $_bootstrap;

    /**
     * Constructor
     *
     * @param \Magento\TestFramework\Bootstrap $bootstrap
     */
    public function __construct(\Magento\TestFramework\Bootstrap $bootstrap)
    {
        $this->_bootstrap = $bootstrap;
    }

    /**
     * Self instance getter
     *
     * @return Bootstrap
     * @throws LocalizedException
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            throw new LocalizedException(__('Helper instance is not defined yet.'));
        }
        return self::$_instance;
    }

    /**
     * Set self instance for static access
     *
     * @param Bootstrap $instance
     * @throws LocalizedException
     */
    public static function setInstance(Bootstrap $instance)
    {
        if (self::$_instance) {
            throw new LocalizedException(__('Helper instance cannot be redefined.'));
        }
        self::$_instance = $instance;
    }

    /**
     * Check the possibility to send headers or to use headers related function (like set_cookie)
     *
     * @return bool
     */
    public static function canTestHeaders()
    {
        if (!headers_sent() && extension_loaded('xdebug') && function_exists('xdebug_get_headers')) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve object manager
     *
     * @return ObjectManagerInterface
     */
    public static function getObjectManager()
    {
        return self::$_objectManager;
    }

    /**
     * Set object manager
     *
     * @param ObjectManagerInterface $objectManager
     */
    public static function setObjectManager(ObjectManagerInterface $objectManager)
    {
        self::$_objectManager = $objectManager;
    }

    /**
     * Retrieve application installation directory
     *
     * @return string
     */
    public function getAppTempDir()
    {
        return $this->_bootstrap->getApplication()->getTempDir();
    }

    /**
     * Retrieve application initialization options
     *
     * @return array
     */
    public function getAppInitParams()
    {
        return $this->_bootstrap->getApplication()->getInitParams();
    }

    /**
     * Reinitialize the application instance optionally passing parameters to be overridden.
     * Intended to be used for the tests isolation purposes.
     *
     * @param array $overriddenParams
     */
    public function reinitialize(array $overriddenParams = [])
    {
        $this->_bootstrap->getApplication()->reinitialize($overriddenParams);
    }

    /**
     * Perform the full request processing by the application instance.
     * Intended to be used by the controller tests.
     */
    public function runApp()
    {
        $this->_bootstrap->getApplication()->run();
    }

    /**
     * Get bootstrap object
     *
     * @return \Magento\TestFramework\Bootstrap
     */
    public function getBootstrap()
    {
        return $this->_bootstrap;
    }

    /**
     * Load area
     * @param string $areaCode
     */
    public function loadArea($areaCode)
    {
        self::$_objectManager->get(State::class)->setAreaCode($areaCode);
        self::$_objectManager->get(AreaList::class)->getArea($areaCode)->load();
    }
}
