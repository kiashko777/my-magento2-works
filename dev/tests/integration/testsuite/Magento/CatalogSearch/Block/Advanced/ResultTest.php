<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Block\Advanced;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\CatalogSearch\Model\ResourceModel\Advanced\Collection;
use Magento\Framework\View\Element\Text;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    /**
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @var Result
     */
    protected $_block;

    /**
     * @magentoAppIsolation enabled
     */
    public function testSetListOrders()
    {
        $sortOptions = [
            'option1' => 'Label Option 1',
            'position' => 'Label Position',
            'option3' => 'Label Option 2',
        ];
        /** @var Category $category */
        $category = $this->createPartialMock(Category::class, ['getAvailableSortByOptions']);
        $category->expects($this->atLeastOnce())
            ->method('getAvailableSortByOptions')
            ->willReturn($sortOptions);
        $category->setId(100500); // Any id - just for layer navigation
        /** @var Resolver $resolver */
        $resolver = Bootstrap::getObjectManager()
            ->get(Resolver::class);
        $resolver->get()->setCurrentCategory($category);

        $childBlock = $this->_layout->addBlock(
            Text::class,
            'search_result_list',
            'block'
        );

        $expectedOptions = ['option1' => 'Label Option 1', 'option3' => 'Label Option 2'];
        $this->assertNotEquals($expectedOptions, $childBlock->getAvailableOrders());
        $this->_block->setListOrders();
        $this->assertEquals($expectedOptions, $childBlock->getAvailableOrders());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testSetListModes()
    {
        /** @var $childBlock Text */
        $childBlock = $this->_layout->addBlock(
            Text::class,
            'search_result_list',
            'block'
        );
        $this->assertEmpty($childBlock->getModes());
        $this->_block->setListModes();
        $this->assertNotEmpty($childBlock->getModes());
    }

    public function testSetListCollection()
    {
        /** @var $childBlock Text */
        $childBlock = $this->_layout->addBlock(
            Text::class,
            'search_result_list',
            'block'
        );
        $this->assertEmpty($childBlock->getCollection());
        $this->_block->setListCollection();
        $this->assertInstanceOf(
            Collection::class,
            $childBlock->getCollection()
        );
    }

    protected function setUp(): void
    {
        $this->_layout = Bootstrap::getObjectManager()->create(
            LayoutInterface::class
        );
        $this->_block = $this->_layout->createBlock(Result::class, 'block');
    }
}
