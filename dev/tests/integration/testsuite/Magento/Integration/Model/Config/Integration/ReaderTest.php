<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magento\Integration\Model\Config\Integration;

use Magento\Framework\Config\FileResolverInterface;
use Magento\Integration\Model\Config\Integration\Reader as ConfigReader;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Integration API config reader test.
 */
class ReaderTest extends TestCase
{
    /** @var MockObject */
    protected $_fileResolverMock;

    /** @var ConfigReader */
    protected $_configReader;

    public function testRead()
    {
        $configFiles = [
            file_get_contents(realpath(__DIR__ . '/_files/apiA.xml')),
            file_get_contents(realpath(__DIR__ . '/_files/apiB.xml')),
        ];
        $this->_fileResolverMock->expects($this->any())->method('get')->willReturn($configFiles);

        $expectedResult = require __DIR__ . '/_files/api.php';
        $this->assertEquals($expectedResult, $this->_configReader->read(), 'Error happened during config reading.');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->_fileResolverMock = $this->createMock(FileResolverInterface::class);
        $objectManager = Bootstrap::getObjectManager();
        $this->_configReader = $objectManager->create(
            Reader::class,
            ['fileResolver' => $this->_fileResolverMock]
        );
    }
}
