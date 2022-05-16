<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cms\Model;

use Magento\Backend\Model\Auth\Session;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class PageTest extends TestCase
{
    /**
     * Tests the get by identifier command
     * @param array $pageData
     * @throws NoSuchEntityException
     * @magentoDbIsolation enabled
     * @dataProvider testGetByIdentifierDataProvider
     */
    public function testGetByIdentifier(array $pageData)
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var GetPageByIdentifier $getPageByIdentifierCommand */
        /** @var \Magento\Cms\Model\ResourceModel\Page $pageResource */
        /** @var PageFactory $pageFactory */
        $pageFactory = $objectManager->create(PageFactory::class);
        $pageResource = $objectManager->create(\Magento\Cms\Model\ResourceModel\Page::class);
        $getPageByIdentifierCommand = $objectManager->create(GetPageByIdentifier::class);

        # Prepare and save the temporary page
        $tempPage = $pageFactory->create();
        $tempPage->setData($pageData);
        $pageResource->save($tempPage);

        # Load previously created block and compare identifiers
        $storeId = reset($pageData['stores']);
        $page = $getPageByIdentifierCommand->execute($pageData['identifier'], $storeId);
        $this->assertEquals($pageData['identifier'], $page->getIdentifier());
    }

    /**
     * @param array $data
     * @param string $expectedIdentifier
     * @magentoDbIsolation enabled
     * @dataProvider generateIdentifierFromTitleDataProvider
     */
    public function testGenerateIdentifierFromTitle($data, $expectedIdentifier)
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var Page $page */
        $page = $objectManager->create(Page::class);
        $page->setData($data);
        $page->save();
        $this->assertEquals($expectedIdentifier, $page->getIdentifier());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testUpdateTime()
    {
        $objectManager = Bootstrap::getObjectManager();

        /**
         * @var $db AdapterInterface
         */
        $db = $objectManager->get(ResourceConnection::class)
            ->getConnection(ResourceConnection::DEFAULT_CONNECTION);

        /** @var Page $page */
        $page = $objectManager->create(Page::class);
        $page->setData(['title' => 'Test', 'stores' => [1]]);
        $beforeTimestamp = $db->fetchCol('SELECT UNIX_TIMESTAMP()')[0];
        $page->save();
        $afterTimestamp = $db->fetchCol('SELECT UNIX_TIMESTAMP()')[0];
        $page = $objectManager->get(PageRepositoryInterface::class)->getById($page->getId());
        $pageTimestamp = strtotime($page->getUpdateTime());

        /*
         * These checks prevent a race condition MAGETWO-95534
         */
        $this->assertGreaterThanOrEqual($beforeTimestamp, $pageTimestamp);
        $this->assertLessThanOrEqual($afterTimestamp, $pageTimestamp);
    }

    public function generateIdentifierFromTitleDataProvider(): array
    {
        return [
            ['data' => ['title' => 'Test title', 'stores' => [1]], 'expectedIdentifier' => 'test-title'],
            [
                'data' => ['title' => 'Кирилический заголовок', 'stores' => [1]],
                'expectedIdentifier' => 'kirilicheskij-zagolovok'
            ],
            [
                'data' => ['title' => 'Test title', 'identifier' => 'custom-identifier', 'stores' => [1]],
                'expectedIdentifier' => 'custom-identifier'
            ]
        ];
    }

    /**
     * Data provider for "testGetByIdentifier" method
     * @return array
     */
    public function testGetByIdentifierDataProvider(): array
    {
        return [
            ['data' => [
                'title' => 'Test title',
                'identifier' => 'test-identifier',
                'page_layout' => '1column',
                'stores' => [1],
                'content' => 'Test content',
                'is_active' => 1
            ]]
        ];
    }

    protected function setUp(): void
    {
        $user = Bootstrap::getObjectManager()->create(
            User::class
        )->loadByUsername(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME
        );

        /** @var $session Session */
        $session = Bootstrap::getObjectManager()->get(
            Session::class
        );
        $session->setUser($user);
    }
}
