<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework;

use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * @var MutableScopeConfigInterface
     */
    private $mutableScopeConfig;

    /**
     * @var array
     */
    private $configData;

    /**
     * @param MutableScopeConfigInterface $mutableScopeConfig
     * @param string $configPath
     */
    public function __construct(MutableScopeConfigInterface $mutableScopeConfig, $configPath)
    {
        $this->mutableScopeConfig = $mutableScopeConfig;
        $this->configData = $this->readFile($configPath);
    }

    /**
     * @param string $configPath
     * @return array
     */
    private function readFile($configPath)
    {
        /** @var array $config */
        $config = require $configPath;

        return $config;
    }

    /**
     * Rewrite config from integration config to global config
     */
    public function rewriteAdditionalConfig()
    {
        foreach ($this->configData as $path => $value) {
            $this->mutableScopeConfig->setValue($path, $value, ScopeInterface::SCOPE_STORE);
        }
    }
}
