<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Widget\Model\Config;

use Magento\Framework\Config\FileResolverInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    /**
     * @var Reader
     */
    private $model;

    /**
     * @var MockObject
     */
    private $fileResolver;

    public function testRead()
    {
        $this->fileResolver->expects($this->once())
            ->method('get')
            ->willReturn([file_get_contents(__DIR__ . '/_files/orders_and_returns.xml')]);
        $expected = include __DIR__ . '/_files/expectedGlobalArray.php';
        $this->assertEquals($expected, $this->model->read('global'));
    }

    public function testReadFile()
    {
        $file = file_get_contents(__DIR__ . '/_files/orders_and_returns.xml');
        $expected = include __DIR__ . '/_files/expectedGlobalArray.php';
        $this->assertEquals($expected, $this->model->readFile($file));
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = [
            file_get_contents(__DIR__ . '/_files/catalog_new_products_list.xml'),
            file_get_contents(__DIR__ . '/_files/orders_and_returns_customized.xml'),
        ];
        $this->fileResolver->expects($this->once())
            ->method('get')
            ->with('widget.xml', 'global')
            ->willReturn($fileList);
        $expected = include __DIR__ . '/_files/expectedMergedArray.php';
        $this->assertEquals($expected, $this->model->read('global'));
    }

    protected function setUp(): void
    {
        $this->fileResolver = $this->getMockForAbstractClass(FileResolverInterface::class);
        $objectManager = Bootstrap::getObjectManager();
        $this->model = $objectManager->create(
            Reader::class,
            ['fileResolver' => $this->fileResolver]
        );
    }
}
