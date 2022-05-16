<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch6\SearchAdapter;

use Magento\Elasticsearch\Elasticsearch5\SearchAdapter\ConnectionManager;
use Magento\Elasticsearch6\Model\Client\Elasticsearch;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Elasticsearch\SearchAdapter\ConnectionManager class.
 */
class ConnectionManagerTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Elasticsearch\SearchAdapter\ConnectionManager
     */
    private $connectionManager;

    /**
     * Test if 'elasticsearch5' search engine returned by connection manager.
     *
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/catalog/search/engine elasticsearch5
     */
    public function testCorrectElasticsearchClientEs5()
    {
        $connection = $this->connectionManager->getConnection();
        $this->assertInstanceOf(
            \Magento\Elasticsearch\Elasticsearch5\Model\Client\Elasticsearch::class,
            $connection
        );
    }

    /**
     * Test if 'elasticsearch6' search engine returned by connection manager.
     *
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/catalog/search/engine elasticsearch6
     */
    public function testCorrectElasticsearchClientEs6()
    {
        $connection = $this->connectionManager->getConnection();
        $this->assertInstanceOf(
            Elasticsearch::class,
            $connection
        );
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->connectionManager = $this->objectManager->create(ConnectionManager::class);
    }
}
