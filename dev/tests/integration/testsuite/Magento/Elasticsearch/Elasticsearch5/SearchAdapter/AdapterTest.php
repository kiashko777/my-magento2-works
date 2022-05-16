<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch\Elasticsearch5\SearchAdapter;

use DOMDocument;
use Exception;
use Magento\AdvancedSearch\Model\Client\ClientInterface;
use Magento\Elasticsearch\SearchAdapter\ConnectionManager;
use Magento\Framework\Search\Request\Builder;
use Magento\Framework\Search\Request\Config;
use Magento\Framework\Search\Request\Config\Converter;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class AdapterTest to test Elasticsearch search adapter
 */
class AdapterTest extends TestCase
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var ClientInterface|MockObject
     */
    private $clientMock;

    /**
     * @var Builder
     */
    private $requestBuilder;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/elasticsearch_index_prefix adaptertest
     * @return void
     */
    public function testQuery()
    {
        $this->requestBuilder->bind('fulltext_search_query', 'socks');
        $this->requestBuilder->setRequestName('one_match');
        $queryRequest = $this->requestBuilder->create();
        $exception = new Exception('Test Message');
        $this->loggerMock->expects($this->once())->method('critical')->with($exception);
        $this->clientMock->expects($this->once())->method('query')->willThrowException($exception);
        $actualResponse = $this->adapter->query($queryRequest);
        $this->assertEmpty($actualResponse->getAggregations()->getBuckets());
        $this->assertEquals(0, $actualResponse->count());
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $contentManager = $this->getMockBuilder(ConnectionManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->clientMock = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['query', 'testConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $contentManager
            ->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->clientMock);
        /** @var Converter $converter */
        $converter = $objectManager->create(Converter::class);

        $document = new DOMDocument();
        $document->load($this->getRequestConfigPath());
        $requestConfig = $converter->convert($document);

        /** @var Config $config */
        $config = $objectManager->create(Config::class);
        $config->merge($requestConfig);

        $this->requestBuilder = $objectManager->create(
            Builder::class,
            ['config' => $config]
        );
        $this->loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);

        $this->adapter = $objectManager->create(
            Adapter::class,
            [
                'connectionManager' => $contentManager,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Get request config path
     *
     * @return string
     */
    private function getRequestConfigPath()
    {
        return __DIR__ . '/../../_files/requests.xml';
    }
}
