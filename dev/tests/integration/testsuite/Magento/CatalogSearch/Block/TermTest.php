<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Block;

use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\Search\Block\Term;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class TermTest extends TestCase
{
    /**
     * @var Term
     */
    protected $_block;

    public function testGetSearchUrl()
    {
        $query = uniqid();
        $obj = new DataObject(['query_text' => $query]);
        $this->assertStringEndsWith("/catalogsearch/result/?q={$query}", $this->_block->getSearchUrl($obj));
    }

    protected function setUp(): void
    {
        $this->_block = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        )->createBlock(
            Term::class
        );
    }
}
