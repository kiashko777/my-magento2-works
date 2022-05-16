<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Webapi\Controller;

use Magento\Framework\Locale\ResolverInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for Magento\Webapi\Controller\PathProcessor class.
 */
class PathProcessorTest extends TestCase
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var PathProcessor
     */
    protected $pathProcessor;
    /**
     * @var ResolverInterface::class
     */
    private $localeResolver;

    /**
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     */
    public function testProcessWithValidStoreCode()
    {
        $storeCode = 'fixturestore';
        $basePath = "rest/{$storeCode}";
        $path = $basePath . '/V1/customerAccounts/createCustomer';
        $resultPath = $this->pathProcessor->process($path);
        $this->assertEquals(str_replace($basePath, "", $path), $resultPath);
        $this->assertEquals($storeCode, $this->storeManager->getStore()->getCode());
    }

    public function testProcessWithAllStoreCode()
    {
        $storeCode = 'all';
        $path = '/V1/customerAccounts/createCustomer';
        $uri = 'rest/' . $storeCode . $path;
        $result = $this->pathProcessor->process($uri);
        $this->assertEquals($path, $result);
        $this->assertEquals(Store::ADMIN_CODE, $this->storeManager->getStore()->getCode());
    }

    public function testProcessWithoutStoreCode()
    {
        $path = '/V1/customerAccounts/createCustomer';
        $uri = 'rest' . $path;
        $result = $this->pathProcessor->process($uri);
        $this->assertEquals($path, $result);
        $this->assertEquals('default', $this->storeManager->getStore()->getCode());
    }

    /**
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoConfigFixture default_store general/locale/code en_US
     * @magentoConfigFixture fixturestore_store general/locale/code de_DE
     */
    public function testProcessWithValidStoreCodeApplyLocale()
    {
        $locale = 'de_DE';
        $storeCode = 'fixturestore';
        $basePath = "rest/{$storeCode}";
        $path = $basePath . '/V1/customerAccounts/createCustomer';
        $this->pathProcessor->process($path);
        $this->assertEquals($locale, $this->localeResolver->getLocale());
        $this->assertNotEquals('en_US', $this->localeResolver->getLocale());
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->storeManager = $objectManager->get(StoreManagerInterface::class);
        $this->storeManager->reinitStores();
        $this->localeResolver = $objectManager->get(ResolverInterface::class);
        $this->pathProcessor = $objectManager->get(PathProcessor::class);
    }
}
