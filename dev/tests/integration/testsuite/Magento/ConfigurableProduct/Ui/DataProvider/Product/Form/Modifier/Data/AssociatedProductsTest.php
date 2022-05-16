<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\Data;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePanel;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Ui\Component\Filters\FilterModifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test verifies modifier for configurable associated product
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AssociatedProductsTest extends TestCase
{
    /**
     * @var ObjectManagerInterface $objectManager
     */
    private $objectManager;

    /**
     * @var Registry $registry
     */
    private $registry;

    /**
     * @dataProvider getProductMatrixDataProvider
     * @param string $interfaceLocale
     * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     * @magentoAppArea Adminhtml
     */
    public function testGetProductMatrix($interfaceLocale)
    {
        $productSku = 'configurable';
        $associatedProductsData = [
            [10 => '10.000000'],
            [20 => '20.000000']
        ];
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        $this->registry->register('current_product', $productRepository->get($productSku));
        /** @var $store Store */
        $store = $this->objectManager->create(Store::class);
        $store->load('admin');
        $this->registry->register('current_store', $store);
        /** @var ResolverInterface|MockObject $localeResolver */
        $localeResolver = $this->getMockBuilder(ResolverInterface::class)
            ->setMethods(['getLocale'])
            ->getMockForAbstractClass();
        $localeResolver->expects($this->any())->method('getLocale')->willReturn($interfaceLocale);
        $localeCurrency = $this->objectManager->create(
            CurrencyInterface::class,
            ['localeResolver' => $localeResolver]
        );
        $associatedProducts = $this->objectManager->create(
            AssociatedProducts::class,
            ['localeCurrency' => $localeCurrency]
        );
        foreach ($associatedProducts->getProductMatrix() as $productMatrixId => $productMatrixData) {
            $this->assertEquals(
                $associatedProductsData[$productMatrixId][$productMatrixData['id']],
                $productMatrixData['price']
            );
        }
    }

    /**
     * Tests configurable product won't appear in product listing.
     *
     * Tests configurable product won't appear in configurable associated product listing if its options attribute
     * is not filterable in grid.
     *
     * @return void
     * @magentoDataFixture Magento/ConfigurableProduct/_files/product_configurable.php
     * @magentoAppArea Adminhtml
     */
    public function testAddManuallyConfigurationsWithNotFilterableInGridAttribute(): void
    {
        /** @var RequestInterface $request */
        $request = $this->objectManager->get(RequestInterface::class);
        $request->setParams([
            FilterModifier::FILTER_MODIFIER => [
                'test_configurable' => [
                    'condition_type' => 'notnull',
                ],
            ],
            'attributes_codes' => [
                'test_configurable'
            ],
        ]);
        $context = $this->objectManager->create(ContextInterface::class, ['request' => $request]);
        /** @var UiComponentFactory $uiComponentFactory */
        $uiComponentFactory = $this->objectManager->get(UiComponentFactory::class);
        $uiComponent = $uiComponentFactory->create(
            ConfigurablePanel::ASSOCIATED_PRODUCT_LISTING,
            null,
            ['context' => $context]
        );

        foreach ($uiComponent->getChildComponents() as $childUiComponent) {
            $childUiComponent->prepare();
        }

        $dataSource = $uiComponent->getDataSourceData();
        $skus = array_column($dataSource['data']['items'], 'sku');

        $this->assertNotContains(
            'configurable',
            $skus,
            'Only products with specified attribute should be in product list'
        );
    }

    /**
     * @return array
     */
    public function getProductMatrixDataProvider()
    {
        return [
            ['en_US'],
            ['zh_Hans_CN']
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->registry = $this->objectManager->get(Registry::class);
    }
}
