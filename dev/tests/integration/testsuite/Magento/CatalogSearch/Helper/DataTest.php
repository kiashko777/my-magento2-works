<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogSearch\Helper;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $_helper;

    public function testGetResultUrl()
    {
        $this->assertStringEndsWith('/catalogsearch/result/', $this->_helper->getResultUrl());

        $query = uniqid();
        $this->assertStringEndsWith("/catalogsearch/result/?q={$query}", $this->_helper->getResultUrl($query));
    }

    public function testGetAdvancedSearchUrl()
    {
        $this->assertStringEndsWith('/catalogsearch/advanced/', $this->_helper->getAdvancedSearchUrl());
    }

    public function testCheckNotesResult()
    {
        $this->assertInstanceOf(Data::class, $this->_helper->checkNotes());
    }

    protected function setUp(): void
    {
        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        /** @var Http $request */
        $request = $objectManager->get(RequestInterface::class);
        $request->setParam('q', 'five <words> here <being> tested');
        $this->_helper = Bootstrap::getObjectManager()->get(
            Data::class
        );
    }
}
