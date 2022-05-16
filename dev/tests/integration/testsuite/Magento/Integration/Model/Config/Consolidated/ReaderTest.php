<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magento\Integration\Model\Config\Consolidated;

use Magento\Framework\Config\FileResolverInterface;
use Magento\Integration\Model\Config\Consolidated\Reader as ConfigReader;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Integration config reader test.
 */
class ReaderTest extends TestCase
{
    /** @var MockObject */
    protected $fileResolverMock;

    /** @var ConfigReader */
    protected $configReader;

    public function testRead()
    {
        $configFiles = [
            file_get_contents(realpath(__DIR__ . '/_files/integrationA.xml')),
            file_get_contents(realpath(__DIR__ . '/_files/integrationB.xml'))
        ];
        $this->fileResolverMock->expects($this->any())->method('get')->willReturn($configFiles);

        $expectedResult = require __DIR__ . '/_files/integration.php';
        $this->assertEquals($expectedResult, $this->configReader->read(), 'Error happened during config reading.');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileResolverMock = $this->getMockBuilder(FileResolverInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $objectManager = Bootstrap::getObjectManager();
        $this->configReader = $objectManager->create(
            Reader::class,
            ['fileResolver' => $this->fileResolverMock]
        );
    }
}
