<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Model;

use Laminas\Stdlib\Parameters;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ProductRepository;
use Magento\Config\Model\ResourceModel\Config\Data;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\TestFramework\Entity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:disable Magento2.Security.Superglobal
 */
class StoreTest extends TestCase
{
    /**
     * @var array
     */
    protected $modelParams;

    /**
     * @var Store|MockObject
     */
    protected $model;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @param $loadId
     * @param $expectedId
     * @dataProvider loadDataProvider
     */
    public function testLoad($loadId, $expectedId)
    {
        $this->model->load($loadId);
        $this->assertEquals($expectedId, $this->model->getId());
    }

    /**
     * @return array
     */
    public function loadDataProvider()
    {
        return [[1, 1], ['default', 1], ['nostore', null]];
    }

    public function testSetGetWebsite()
    {
        $this->assertFalse($this->model->getWebsite());
        $website = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            StoreManagerInterface::class
        )->getWebsite();
        $this->model->setWebsite($website);
        $actualResult = $this->model->getWebsite();
        $this->assertSame($website, $actualResult);
    }

    public function testSetGetGroup()
    {
        $this->assertFalse($this->model->getGroup());
        $storeGroup = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            StoreManager::class
        )->getGroup();
        $this->model->setGroup($storeGroup);
        $actualResult = $this->model->getGroup();
        $this->assertSame($storeGroup, $actualResult);
    }

    /**
     * Isolation is enabled, as we pollute config with rewrite values
     *
     * @param string $type
     * @param bool $useRewrites
     * @param bool $useStoreCode
     * @param string $expected
     * @dataProvider getBaseUrlDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetBaseUrl($type, $useRewrites, $useStoreCode, $expected)
    {
        /* config operations require store to be loaded */
        $this->model->load('default');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(MutableScopeConfigInterface::class)
            ->setValue(Store::XML_PATH_USE_REWRITES, $useRewrites, ScopeInterface::SCOPE_STORE);

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(MutableScopeConfigInterface::class)
            ->setValue(Store::XML_PATH_STORE_IN_URL, $useStoreCode, ScopeInterface::SCOPE_STORE);

        $actual = $this->model->getBaseUrl($type);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function getBaseUrlDataProvider()
    {
        return [
            [UrlInterface::URL_TYPE_WEB, false, false, 'http://localhost/'],
            [UrlInterface::URL_TYPE_WEB, false, true, 'http://localhost/'],
            [UrlInterface::URL_TYPE_WEB, true, false, 'http://localhost/'],
            [UrlInterface::URL_TYPE_WEB, true, true, 'http://localhost/'],
            [UrlInterface::URL_TYPE_LINK, false, false, 'http://localhost/index.php/'],
            [UrlInterface::URL_TYPE_LINK, false, true, 'http://localhost/index.php/default/'],
            [UrlInterface::URL_TYPE_LINK, true, false, 'http://localhost/'],
            [UrlInterface::URL_TYPE_LINK, true, true, 'http://localhost/default/'],
            [UrlInterface::URL_TYPE_DIRECT_LINK, false, false, 'http://localhost/index.php/'],
            [UrlInterface::URL_TYPE_DIRECT_LINK, false, true, 'http://localhost/index.php/'],
            [UrlInterface::URL_TYPE_DIRECT_LINK, true, false, 'http://localhost/'],
            [UrlInterface::URL_TYPE_DIRECT_LINK, true, true, 'http://localhost/'],
            [UrlInterface::URL_TYPE_STATIC, false, false, 'http://localhost/static/'],
            [UrlInterface::URL_TYPE_STATIC, false, true, 'http://localhost/static/'],
            [UrlInterface::URL_TYPE_STATIC, true, false, 'http://localhost/static/'],
            [UrlInterface::URL_TYPE_STATIC, true, true, 'http://localhost/static/'],
            [UrlInterface::URL_TYPE_MEDIA, false, false, 'http://localhost/media/'],
            [UrlInterface::URL_TYPE_MEDIA, false, true, 'http://localhost/media/'],
            [UrlInterface::URL_TYPE_MEDIA, true, false, 'http://localhost/media/'],
            [UrlInterface::URL_TYPE_MEDIA, true, true, 'http://localhost/media/']
        ];
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetBaseUrlInPub()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(
            [
                Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS => [
                    DirectoryList::PUB => [DirectoryList::URL_PATH => ''],
                ],
            ]
        );

        $this->model = $this->_getStoreModel();
        $this->model->load('default');

        $this->assertEquals('http://localhost/static/', $this->model->getBaseUrl(UrlInterface::URL_TYPE_STATIC));
        $this->assertEquals('http://localhost/media/', $this->model->getBaseUrl(UrlInterface::URL_TYPE_MEDIA));
    }

    /**
     * Isolation is enabled, as we pollute config with rewrite values
     *
     * @param string $type
     * @param bool $useCustomEntryPoint
     * @param bool $useStoreCode
     * @param string $expected
     * @dataProvider getBaseUrlForCustomEntryPointDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetBaseUrlForCustomEntryPoint($type, $useCustomEntryPoint, $useStoreCode, $expected)
    {
        /* config operations require store to be loaded */
        $this->model->load('default');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(MutableScopeConfigInterface::class)
            ->setValue(Store::XML_PATH_USE_REWRITES, false, ScopeInterface::SCOPE_STORE);

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(MutableScopeConfigInterface::class)
            ->setValue(Store::XML_PATH_STORE_IN_URL, $useStoreCode, ScopeInterface::SCOPE_STORE);

        // emulate custom entry point
        $_SERVER['SCRIPT_FILENAME'] = 'custom_entry.php';
        $request = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get(RequestInterface::class);
        $request->setServer(new Parameters($_SERVER));

        if ($useCustomEntryPoint) {
            $property = new ReflectionProperty($this->model, '_isCustomEntryPoint');
            $property->setAccessible(true);
            $property->setValue($this->model, $useCustomEntryPoint);
        }
        $actual = $this->model->getBaseUrl($type);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function getBaseUrlForCustomEntryPointDataProvider()
    {
        return [
            [UrlInterface::URL_TYPE_LINK, false, false, 'http://localhost/custom_entry.php/'],
            [
                UrlInterface::URL_TYPE_LINK,
                false,
                true,
                'http://localhost/custom_entry.php/default/'
            ],
            [UrlInterface::URL_TYPE_LINK, true, false, 'http://localhost/index.php/'],
            [UrlInterface::URL_TYPE_LINK, true, true, 'http://localhost/index.php/default/'],
            [
                UrlInterface::URL_TYPE_DIRECT_LINK,
                false,
                false,
                'http://localhost/custom_entry.php/'
            ],
            [
                UrlInterface::URL_TYPE_DIRECT_LINK,
                false,
                true,
                'http://localhost/custom_entry.php/'
            ],
            [UrlInterface::URL_TYPE_DIRECT_LINK, true, false, 'http://localhost/index.php/'],
            [UrlInterface::URL_TYPE_DIRECT_LINK, true, true, 'http://localhost/index.php/']
        ];
    }

    public function testGetDefaultCurrency()
    {
        /* currency operations require store to be loaded */
        $this->model->load('default');
        $this->assertEquals($this->model->getDefaultCurrencyCode(), $this->model->getDefaultCurrency()->getCode());
    }

    public function testIsCanDelete()
    {
        $this->assertFalse($this->model->isCanDelete());
        $this->model->load(1);
        $this->assertFalse($this->model->isCanDelete());
        $this->model->setId(100);
        $this->assertFalse($this->model->isCanDelete());
    }

    /**
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoAppIsolation enabled
     */
    public function testGetCurrentUrl()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get(MutableScopeConfigInterface::class)
            ->setValue('web/url/use_store', true, ScopeInterface::SCOPE_STORE, 'secondstore');

        $this->model->load('admin');
        $this->model
            ->expects($this->any())->method('getUrl')
            ->willReturn('http://localhost/index.php');
        $this->assertStringEndsWith('default', $this->model->getCurrentUrl());
        $this->assertStringEndsNotWith('default', $this->model->getCurrentUrl(false));

        $this->model
            ->expects($this->any())->method('getUrl')
            ->willReturn('http://localhost/index.php?' . SidResolverInterface::SESSION_ID_QUERY_PARAM . '=12345');
        $this->request->setParams([SidResolverInterface::SESSION_ID_QUERY_PARAM, '12345']);
        $this->request->setQueryValue(SidResolverInterface::SESSION_ID_QUERY_PARAM, '12345');
        $this->assertStringContainsString(
            SidResolverInterface::SESSION_ID_QUERY_PARAM . '=12345',
            $this->model->getCurrentUrl()
        );

        /** @var Store $secondStore */
        $secondStore = $objectManager->get(StoreRepositoryInterface::class)->get('secondstore');

        /** @var ProductRepository $productRepository */
        $productRepository = $objectManager->create(ProductRepository::class);
        $product = $productRepository->get('simple');
        $product->setStoreId($secondStore->getId());
        $url = $product->getUrlInStore();

        $this->assertEquals(
            $secondStore->getBaseUrl() . 'catalog/product/view/id/1/s/simple-product/',
            $url
        );
        $this->assertEquals(
            $secondStore->getBaseUrl() . '?SID=12345&___from_store=default',
            $secondStore->getCurrentUrl()
        );
        $this->assertEquals(
            $secondStore->getBaseUrl() . '?SID=12345',
            $secondStore->getCurrentUrl(false)
        );
    }

    /**
     * @magentoDataFixture Magento/Store/_files/core_second_third_fixturestore.php
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testGetCurrentUrlWithUseStoreInUrlFalse()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get(ReinitableConfigInterface::class)
            ->setValue('web/url/use_store', false, ScopeInterface::SCOPE_STORE, 'default');

        /** @var Store $secondStore */
        $secondStore = $objectManager->get(StoreRepositoryInterface::class)->get('secondstore');

        /** @var ProductRepository $productRepository */
        $productRepository = $objectManager->create(ProductRepository::class);
        $product = $productRepository->get('simple');

        $product->setStoreId($secondStore->getId());
        $url = $product->getUrlInStore();

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $objectManager->get(CategoryRepository::class);
        $category = $categoryRepository->get(2, $secondStore->getStoreId());

        $this->assertEquals(
            $secondStore->getBaseUrl() . 'catalog/category/view/s/default-category/id/2/',
            $category->getUrl()
        );
        $this->assertEquals(
            $secondStore->getBaseUrl() .
            'catalog/product/view/id/1/s/simple-product/?___store=secondstore',
            $url
        );
        $this->assertEquals(
            $secondStore->getBaseUrl() . '?___store=secondstore&___from_store=default',
            $secondStore->getCurrentUrl()
        );
        $this->assertEquals(
            $secondStore->getBaseUrl() . '?___store=secondstore',
            $secondStore->getCurrentUrl(false)
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea Adminhtml
     * @magentoDbIsolation enabled
     */
    public function testCRUD()
    {
        $this->model->setData(
            [
                'code' => 'test',
                'website_id' => 1,
                'group_id' => 1,
                'name' => 'test name',
                'sort_order' => 0,
                'is_active' => 1,
            ]
        );
        $crud = new Entity(
            $this->model,
            ['name' => 'new name'],
            Store::class
        );
        $crud->testCrud();
    }

    /**
     * @param array $badStoreData
     *
     * @dataProvider saveValidationDataProvider
     * @magentoAppIsolation enabled
     * @magentoAppArea Adminhtml
     * @magentoDbIsolation enabled
     */
    public function testSaveValidation($badStoreData)
    {
        $this->expectException(LocalizedException::class);

        $normalStoreData = [
            'code' => 'test',
            'website_id' => 1,
            'group_id' => 1,
            'name' => 'test name',
            'sort_order' => 0,
            'is_active' => 1,
        ];
        $data = array_merge($normalStoreData, $badStoreData);
        $this->model->setData($data);
        $this->model->save();
    }

    /**
     * @return array
     */
    public function saveValidationDataProvider()
    {
        return [
            'empty store name' => [['name' => '']],
            'empty store code' => [['code' => '']],
            'invalid store code' => [['code' => '^_^']]
        ];
    }

    /**
     * @param $storeInUrl
     * @param $disableStoreInUrl
     * @param $expectedResult
     * @dataProvider isUseStoreInUrlDataProvider
     */
    public function testIsUseStoreInUrl($storeInUrl, $disableStoreInUrl, $expectedResult)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $configMock = $this->createMock(ReinitableConfigInterface::class);
        $appStateMock = $this->createMock(State::class);

        $params = $this->modelParams;
        $params['context'] = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Framework\Model\Context::class, ['appState' => $appStateMock]);

        $configMock->expects($this->any())
            ->method('getValue')
            ->with($this->stringContains(Store::XML_PATH_STORE_IN_URL))
            ->willReturn($storeInUrl);

        $params['config'] = $configMock;
        $model = $objectManager->create(Store::class, $params);
        $model->setDisableStoreInUrl($disableStoreInUrl);
        $this->assertEquals($expectedResult, $model->isUseStoreInUrl());
    }

    /**
     * @return array
     * @see self::testIsUseStoreInUrl;
     */
    public function isUseStoreInUrlDataProvider()
    {
        return [
            [true, null, true],
            [false, null, false],
            [true, true, false],
            [true, false, true]
        ];
    }

    /**
     * @dataProvider isCurrentlySecureDataProvider
     *
     * @param bool $expected
     * @param array $serverValues
     * @magentoConfigFixture current_store web/secure/offloader_header X_FORWARDED_PROTO
     * @magentoConfigFixture current_store web/secure/base_url https://example.com:80
     */
    public function testIsCurrentlySecure($expected, $serverValues)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var Store $model */
        $model = $objectManager->create(Store::class);

        $request = $objectManager->get(RequestInterface::class);
        $request->setServer(new Parameters(array_merge($_SERVER, $serverValues)));

        $this->assertEquals($expected, $model->isCurrentlySecure());
    }

    public function isCurrentlySecureDataProvider()
    {
        return [
            [true, ['HTTPS' => 'on']],
            [true, ['X_FORWARDED_PROTO' => 'https']],
            [true, ['HTTP_X_FORWARDED_PROTO' => 'https']],
            [true, ['HTTPS' => 'on', 'SERVER_PORT' => 80]],
            [false, ['SERVER_PORT' => 80]],
            [false, []],
        ];
    }

    /**
     * @magentoConfigFixture current_store web/secure/offloader_header SSL_OFFLOADED
     * @magentoConfigFixture current_store web/secure/base_url
     */
    public function testIsCurrentlySecureNoSecureBaseUrl()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var Store $model */
        $model = $objectManager->create(Store::class);

        $server = $_SERVER;
        $_SERVER['SERVER_PORT'] = 80;

        $this->assertFalse($model->isCurrentlySecure());
        $_SERVER = $server;
    }

    protected function setUp(): void
    {
        $this->model = $this->_getStoreModel();
    }

    /**
     * @return MockObject|Store
     */
    protected function _getStoreModel()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->request = $objectManager->get(RequestInterface::class);
        $this->modelParams = [
            'context' => $objectManager->get(\Magento\Framework\Model\Context::class),
            'registry' => $objectManager->get(Registry::class),
            'extensionFactory' => $objectManager->get(ExtensionAttributesFactory::class),
            'customAttributeFactory' => $objectManager->get(AttributeValueFactory::class),
            'resource' => $objectManager->get(\Magento\Store\Model\ResourceModel\Store::class),
            'coreFileStorageDatabase' => $objectManager->get(Database::class),
            'configCacheType' => $objectManager->get(Config::class),
            'url' => $objectManager->get(Url::class),
            'request' => $this->request,
            'configDataResource' => $objectManager->get(Data::class),
            'filesystem' => $objectManager->get(Filesystem::class),
            'config' => $objectManager->get(ReinitableConfigInterface::class),
            'storeManager' => $objectManager->get(StoreManager::class),
            'sidResolver' => $objectManager->get(SidResolverInterface::class),
            'httpContext' => $objectManager->get(Context::class),
            'session' => $objectManager->get(SessionManagerInterface::class),
            'currencyFactory' => $objectManager->get(CurrencyFactory::class),
            'information' => $objectManager->get(Information::class),
            'currencyInstalled' => 'system/currency/installed',
            'groupRepository' => $objectManager->get(GroupRepositoryInterface::class),
            'websiteRepository' => $objectManager->get(WebsiteRepositoryInterface::class),
        ];

        return $this->getMockBuilder(Store::class)
            ->setMethods(['getUrl'])
            ->setConstructorArgs($this->modelParams)
            ->getMock();
    }

    protected function tearDown(): void
    {
        $this->model = null;
    }
}
