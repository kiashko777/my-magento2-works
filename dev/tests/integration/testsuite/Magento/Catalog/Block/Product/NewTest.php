<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Block\Product;

use Magento\Catalog\Block\Product\Widget\NewWidget;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Area;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Magento\Catalog\Block\Products\New.
 *
 * @magentoDataFixture Magento/Catalog/_files/products_new.php
 * @magentoDbIsolation disabled
 */
class NewTest extends TestCase
{
    /**
     * @var NewProduct
     */
    protected $_block;

    public function testGetCacheKeyInfo()
    {
        $info = $this->_block->getCacheKeyInfo();
        $keys = array_keys($info);

        /** order and values of cache key info elements is important */

        $this->assertSame(0, array_shift($keys));
        $this->assertEquals('CATALOG_PRODUCT_NEW', $info[0]);

        $this->assertSame(1, array_shift($keys));
        $this->assertEquals(
            Bootstrap::getObjectManager()->get(
                StoreManagerInterface::class
            )->getStore()->getId(),
            $info[1]
        );

        $this->assertSame(2, array_shift($keys));

        $themeModel = Bootstrap::getObjectManager()->get(
            DesignInterface::class
        )->getDesignTheme();

        $this->assertEquals($themeModel->getId() ?: null, $info[2]);

        $this->assertSame(3, array_shift($keys));
        $this->assertEquals(
            Bootstrap::getObjectManager()->get(
                Session::class
            )->getCustomerGroupId(),
            $info[3]
        );

        $this->assertSame('template', array_shift($keys));

        /**
         * This block is implemented without template by default (invalid).
         * Having the cache key fragment with empty value can potentially lead to caching bugs
         */
        $this->assertSame(4, array_shift($keys));
        $this->assertNotEquals('', $info[4]);
    }

    public function testSetGetProductsCount()
    {
        $this->assertEquals(
            NewProduct::DEFAULT_PRODUCTS_COUNT,
            $this->_block->getProductsCount()
        );
        $this->_block->setProductsCount(100);
        $this->assertEquals(100, $this->_block->getProductsCount());
    }

    public function testToHtml()
    {
        $this->assertEmpty($this->_block->getProductCollection());

        $this->_block->setProductsCount(5);
        $this->_block->setTemplate('product/widget/new/content/new_list.phtml');
        $this->_block->setLayout(
            Bootstrap::getObjectManager()->get(
                LayoutInterface::class
            )
        );

        $html = $this->_block->toHtml();
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('New Products', $html);
        $this->assertInstanceOf(
            Collection::class,
            $this->_block->getProductCollection()
        );
    }

    /**
     * @covers \Magento\Catalog\Block\Product\Widget\NewWidget::getCacheKeyInfo
     */
    public function testNewWidgetGetCacheKeyInfo()
    {
        $block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            NewWidget::class
        );

        $requestParams = ['test' => 'data'];

        $block->getRequest()->setParams($requestParams);

        $info = $block->getCacheKeyInfo();

        $this->assertEquals('CATALOG_PRODUCT_NEW', $info[0]);
        $this->assertEquals(json_encode($requestParams), $info[8]);
    }

    protected function setUp(): void
    {
        /**
         * @var GroupManagementInterface $groupManagement
         */
        $groupManagement = Bootstrap::getObjectManager()
            ->get(GroupManagementInterface::class);
        $notLoggedInId = $groupManagement->getNotLoggedInGroup()->getId();

        Bootstrap::getInstance()->loadArea(Area::AREA_FRONTEND);
        Bootstrap::getObjectManager()->get(
            \Magento\Framework\App\Http\Context::class
        )->setValue(
            \Magento\Customer\Model\Context::CONTEXT_GROUP,
            $notLoggedInId,
            $notLoggedInId
        );
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            NewProduct::class
        );
    }
}
