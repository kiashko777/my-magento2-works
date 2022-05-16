<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableProduct\Block\Cart\Item\Renderer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Block\Cart\Item\Renderer\Configurable as ConfigurableRenderer;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test \Magento\ConfigurableProduct\Block\Cart\Item\Renderer\Configurable block
 *
 * @magentoAppArea frontend
 */
class ConfigurableTest extends TestCase
{
    /**
     * @var ConfigurableRenderer
     */
    private $block;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/ConfigurableProduct/_files/quote_with_configurable_product.php
     */
    public function testGetProductPriceHtml()
    {
        $productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        $configurableProduct = $productRepository->getById(1);

        $layout = $this->objectManager->get(LayoutInterface::class);
        $layout->createBlock(
            Render::class,
            'product.price.render.default',
            [
                'data' => [
                    'price_render_handle' => 'catalog_product_prices',
                    'use_link_for_as_low_as' => true
                ]
            ]
        );

        $this->block->setItem(
            $this->block->getCheckoutSession()->getQuote()->getAllVisibleItems()[0]
        );
        $html = $this->block->getProductPriceHtml($configurableProduct);
        $this->assertStringContainsString('<span class="price">$10.00</span>', $html);
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->block = $this->objectManager->get(LayoutInterface::class)
            ->createBlock(ConfigurableRenderer::class);
    }
}
