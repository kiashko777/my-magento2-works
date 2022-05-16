<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Observer;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SwitchPriceAttributeScopeOnConfigChangeTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @magentoDbIsolation enabled
     * @magentoAppArea Adminhtml
     */
    public function testPriceAttributeHasScopeGlobal()
    {
        foreach (['price', 'cost', 'special_price'] as $attributeCode) {
            $attribute = $this->objectManager->get(Config::class)->getAttribute(
                'catalog_product',
                $attributeCode
            );
            $this->assertTrue($attribute->isScopeGlobal());
        }
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppArea Adminhtml
     */
    public function testPriceAttributeHasScopeWebsite()
    {
        /** @var ReinitableConfigInterface $config */
        $config = $this->objectManager->get(
            ReinitableConfigInterface::class
        );
        $config->setValue(
            Store::XML_PATH_PRICE_SCOPE,
            Store::PRICE_SCOPE_WEBSITE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        $eventManager = $this->objectManager->get(ManagerInterface::class);
        $eventManager->dispatch(
            "admin_system_config_changed_section_catalog",
            ['website' => 0, 'store' => 0]
        );
        foreach (['price', 'cost', 'special_price'] as $attributeCode) {
            $attribute = $this->objectManager->get(Config::class)->getAttribute(
                'catalog_product',
                $attributeCode
            );
            $this->assertTrue($attribute->isScopeWebsite());
        }
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
