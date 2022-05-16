<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AsynchronousOperations\Ui\Component\DataProvider\Operation\Retriable;

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
    public function testGetTotalCount()
    {
        $objectManager = Bootstrap::getObjectManager();
        $requestData = [
            'uuid' => 'bulk-uuid-5',
        ];
        $request = $objectManager->get(RequestInterface::class);
        $request->setParams($requestData);

        /**
         * @var SearchResult $searchResult
         */
        $searchResult = $objectManager->create(
            SearchResult::class
        );
        $this->assertEquals(1, $searchResult->getTotalCount());
    }
}
