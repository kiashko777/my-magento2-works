<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\ObjectManager;

use Magento\Backend\App\Config as BackendConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\MutableScopeConfig;
use Magento\Framework\App\ReinitableConfig;
use Magento\Framework\ObjectManager\DynamicConfigInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\CookieManager;
use Magento\TestFramework\Store\StoreManager;

/**
 * Class which hold configurations (preferences, etc...) of integration test framework
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Configurator implements DynamicConfigInterface
{
    /**
     * Map application initialization params to Object Manager configuration format.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return [
            'preferences' => [
                CookieManagerInterface::class => CookieManager::class,
                StoreManagerInterface::class => StoreManager::class,
                ScopeConfigInterface::class => \Magento\TestFramework\App\Config::class,
                \Magento\Framework\App\Config::class => \Magento\TestFramework\App\Config::class,
                BackendConfig::class => \Magento\TestFramework\Backend\App\Config::class,
                ReinitableConfig::class => \Magento\TestFramework\App\ReinitableConfig::class,
                MutableScopeConfig::class => \Magento\TestFramework\App\MutableScopeConfig::class,
            ]
        ];
    }
}
