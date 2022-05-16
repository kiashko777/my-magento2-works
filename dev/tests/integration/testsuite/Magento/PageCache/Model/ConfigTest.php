<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\PageCache\Model;

use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\PageCache\Model\Varnish\VclGenerator;
use Magento\PageCache\Model\Varnish\VclGeneratorFactory;
use Magento\PageCache\Model\Varnish\VclTemplateLocator;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;


    public function testGetVclFile()
    {
        $result = $this->config->getVclFile(Config::VARNISH_5_CONFIGURATION_PATH);
        $this->assertEquals(file_get_contents(__DIR__ . '/_files/result.vcl'), $result);
    }

    // @codingStandardsIgnoreStart
    /**
     * @magentoConfigFixture default/system/full_page_cache/varnish/backend_host example.com
     * @magentoConfigFixture default/system/full_page_cache/varnish/backend_port 8080
     * @magentoConfigFixture default/system/full_page_cache/varnish/grace_period 1234
     * @magentoConfigFixture default/system/full_page_cache/varnish/access_list 127.0.0.1,192.168.0.1,127.0.0.2
     * @magentoConfigFixture current_store design/theme/ua_regexp {"_":{"regexp":"\/firefox\/i","value":"Magento\/blank"}}
     * @magentoAppIsolation enabled
     */
    // @codingStandardsIgnoreEnd
    protected function setUp(): void
    {
        $readFactoryMock = $this->createMock(ReadFactory::class);
        $modulesDirectoryMock = $this->createMock(Write::class);
        $readFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->willReturn(
            $modulesDirectoryMock
        );
        $modulesDirectoryMock->expects(
            $this->any()
        )->method(
            'readFile'
        )->willReturn(
            file_get_contents(__DIR__ . '/_files/test.vcl')
        );

        /** @var MockObject $vclTemplateLocator */
        $vclTemplateLocator = $this->getMockBuilder(VclTemplateLocator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTemplate'])
            ->getMock();
        $vclTemplateLocator->expects($this->any())
            ->method('getTemplate')
            ->willReturn(file_get_contents(__DIR__ . '/_files/test.vcl'));

        /** @var MockObject $vclTemplateLocator */
        $vclGeneratorFactory = $this->getMockBuilder(VclGeneratorFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $expectedParams = [
            'backendHost' => 'example.com',
            'backendPort' => '8080',
            'accessList' => explode(',', '127.0.0.1,192.168.0.1,127.0.0.2'),
            'designExceptions' => json_decode('{"_":{"regexp":"\/firefox\/i","value":"Magento\/blank"}}', true),
            'sslOffloadedHeader' => 'X-Forwarded-Proto',
            'gracePeriod' => 1234
        ];
        $vclGeneratorFactory->expects($this->any())
            ->method('create')
            ->with($expectedParams)
            ->willReturn(new VclGenerator(
                $vclTemplateLocator,
                'example.com',
                '8080',
                explode(',', '127.0.0.1,192.168.0.1,127.0.0.2'),
                1234,
                'X-Forwarded-Proto',
                json_decode('{"_":{"regexp":"\/firefox\/i","value":"Magento\/blank"}}', true)
            ));
        $this->config = Bootstrap::getObjectManager()->create(
            Config::class,
            [
                'vclGeneratorFactory' => $vclGeneratorFactory
            ]
        );
    }
}
