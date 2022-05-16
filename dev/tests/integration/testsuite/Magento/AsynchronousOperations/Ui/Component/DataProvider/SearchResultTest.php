<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AsynchronousOperations\Ui\Component\DataProvider;

use Magento\Backend\Model\Auth\Session;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
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
    public function testGetAllIds()
    {
        $objectManager = Bootstrap::getObjectManager();
        $user = $objectManager->create(User::class);
        $user->loadByUsername(\Magento\TestFramework\Bootstrap::ADMIN_NAME);
        $session = $objectManager->get(Session::class);
        $session->setUser($user);

        /** @var SearchResult $searchResult */
        $searchResult = $objectManager->create(
            SearchResult::class
        );
        $this->assertEquals(5, $searchResult->getTotalCount());
    }
}
