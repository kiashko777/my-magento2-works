<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Checkout\Block\Cart;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class GridTest
 * Test to verify default config value for
 * Store->Configuration->Sales->Checkout->Shopping Cart->Number of items to display pager
 */
class GridTest extends TestCase
{
    public function testGetDefaultConfig()
    {
        $configValue = 20;
        /** @var $scopeConfig ScopeConfigInterface */
        $scopeConfig = Bootstrap::getObjectManager()->get(
            ScopeConfigInterface::class
        );
        $defaultConfigValue = $scopeConfig->getValue(
            Grid::XPATH_CONFIG_NUMBER_ITEMS_TO_DISPLAY_PAGER,
            ScopeInterface::SCOPE_STORE
        );
        $errorMessage = 'Default Config value for Store->Configuration->Sales->Checkout->Shopping Cart->'
            . 'Number of items to display pager shouold be ' . $configValue;
        $this->assertEquals($configValue, $defaultConfigValue, $errorMessage);
    }
}
