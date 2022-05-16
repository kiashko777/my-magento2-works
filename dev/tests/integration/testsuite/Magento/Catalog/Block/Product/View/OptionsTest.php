<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Product\View;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Block\Products\View\Options.
 */
class OptionsTest extends TestCase
{
    /**
     * @var Options
     */
    protected $block;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testSetGetProduct()
    {
        $this->assertSame($this->product, $this->block->getProduct());

        $product = $this->objectManager->create(
            Product::class
        );
        $this->block->setProduct($product);
        $this->assertSame($product, $this->block->getProduct());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testGetGroupOfOption()
    {
        $this->assertEquals('default', $this->block->getGroupOfOption('test'));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testGetOptions()
    {
        $options = $this->block->getOptions();
        $this->assertNotEmpty($options);
        foreach ($options as $option) {
            $this->assertInstanceOf(Option::class, $option);
        }
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testHasOptions()
    {
        $this->assertTrue($this->block->hasOptions());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_with_dropdown_option.php
     */
    public function testGetJsonConfig()
    {
        $config = json_decode($this->block->getJsonConfig(), true);
        $configValues = array_values($config);
        $this->assertEquals($this->getExpectedJsonConfig(), array_values($configValues[0]));
    }

    /**
     * Expected data for testGetJsonConfig
     *
     * @return array
     */
    private function getExpectedJsonConfig()
    {
        return [
            0 => [
                'prices' =>
                    ['oldPrice' =>
                        ['amount' => 10, 'adjustments' => []],
                        'basePrice' => ['amount' => 10],
                        'finalPrice' => ['amount' => 10]
                    ],
                'type' => 'fixed',
                'name' => 'drop_down option 1',
            ],
            1 => [
                'prices' =>
                    ['oldPrice' =>
                        ['amount' => 40, 'adjustments' => []],
                        'basePrice' => ['amount' => 40],
                        'finalPrice' => ['amount' => 40],
                    ],
                'type' => 'percent',
                'name' => 'drop_down option 2',
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->productRepository = $this->objectManager->create(ProductRepositoryInterface::class);

        try {
            $this->product = $this->productRepository->get('simple');
        } catch (NoSuchEntityException $e) {
            $this->product = $this->productRepository->get('simple_dropdown_option');
        }

        $this->objectManager->get(Registry::class)->unregister('current_product');
        $this->objectManager->get(Registry::class)->register('current_product', $this->product);

        $this->block = $this->objectManager->get(
            LayoutInterface::class
        )->createBlock(
            Options::class
        );
    }
}
