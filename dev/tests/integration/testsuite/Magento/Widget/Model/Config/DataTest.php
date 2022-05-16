<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magento\Widget\Model\Config;

use Magento\Framework\Config\FileResolverInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class DataTest extends TestCase
{
    /**
     * @magentoCache config disabled
     */
    public function testGet()
    {
        $fileResolver = $this->getMockForAbstractClass(FileResolverInterface::class);
        $fileResolver->expects($this->exactly(3))->method('get')->willReturnMap([
            ['widget.xml', 'global', [file_get_contents(__DIR__ . '/_files/orders_and_returns.xml')]],
            ['widget.xml', 'Adminhtml', []],
            ['widget.xml', 'design', [file_get_contents(__DIR__ . '/_files/orders_and_returns_customized.xml')]],
        ]);
        $objectManager = Bootstrap::getObjectManager();
        $reader = $objectManager->create(Reader::class, ['fileResolver' => $fileResolver]);
        /** @var Data $configData */
        $configData = $objectManager->create(Data::class, ['reader' => $reader]);
        $result = $configData->get();
        $expected = include '_files/expectedGlobalDesignArray.php';
        $this->assertEquals($expected, $result);
    }
}
