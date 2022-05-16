<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Block\Dashboard\Tab\Products;

use Magento\Backend\Block\Dashboard\Tab\Products\Viewed as ViewedProductsTabBlock;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap as BootstrapHelper;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class ViewedTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/reports/options/enabled 1
     */
    public function testGetPreparedCollectionProductPrice()
    {
        /** @var ViewedProductsTabBlock $viewedProductsTabBlock */
        $viewedProductsTabBlock = $this->layout->createBlock(ViewedProductsTabBlock::class);
        $product = $this->productRepository->getById(1);
        $this->eventManager->dispatch('catalog_controller_product_view', ['product' => $product]);

        $collection = $viewedProductsTabBlock->getPreparedCollection();

        $this->assertEquals(
            10,
            $collection->getFirstItem()->getDataByKey('price')
        );
    }

    protected function setUp(): void
    {
        $this->objectManager = BootstrapHelper::getObjectManager();
        $this->layout = $this->objectManager->get(LayoutInterface::class);
        $this->productRepository = $this->objectManager->get(ProductRepository::class);
        $this->eventManager = $this->objectManager->get(EventManager::class);
    }
}
