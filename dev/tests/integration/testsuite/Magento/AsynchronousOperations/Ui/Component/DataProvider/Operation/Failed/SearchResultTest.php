<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AsynchronousOperations\Ui\Component\DataProvider\Operation\Failed;

use Magento\Framework\App\RequestInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class SearchResultTest
 */
class SearchResultTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/AsynchronousOperations/_files/bulk.php
     * @magentoDbIsolation enabled
     * @magentoAppArea Adminhtml
     */
    public function testGetItems()
    {
        $objectManager = Bootstrap::getObjectManager();
        $request = $objectManager->get(RequestInterface::class);
        $requestData = [
            'uuid' => 'bulk-uuid-5',
        ];

        $request->setParams($requestData);

        /** @var \Magento\AsynchronousOperations\Ui\Component\DataProvider\SearchResult $searchResult */
        $searchResult = $objectManager->create(
            SearchResult::class
        );
        $this->assertEquals(1, $searchResult->getTotalCount());
        $expected = $searchResult->getItems();
        $expectedItem = array_shift($expected);
        $this->assertEquals('Test', $expectedItem->getMetaInformation());
        $this->assertEquals('5', $expectedItem->getEntityId());
    }
}
