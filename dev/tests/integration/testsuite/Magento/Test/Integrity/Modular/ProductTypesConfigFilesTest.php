<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Catalog\Model\ProductTypes\Config\Reader;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\DirSearch;
use Magento\Framework\Config\FileIteratorFactory;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ProductTypesConfigFilesTest extends TestCase
{
    /**
     * @var Reader
     */
    protected $_model;

    public function testProductTypesXmlFiles()
    {
        $this->_model->read('global');
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var $moduleDirSearch DirSearch */
        $moduleDirSearch = $objectManager->get(DirSearch::class);
        $fileIteratorFactory = $objectManager->get(FileIteratorFactory::class);
        $xmlFiles = $fileIteratorFactory->create(
            $moduleDirSearch->collectFiles(ComponentRegistrar::MODULE, 'etc/{*/product_types.xml,product_types.xml}')
        );

        $fileResolverMock = $this->createMock(FileResolverInterface::class);
        $fileResolverMock->expects($this->any())->method('get')->willReturn($xmlFiles);
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->expects($this->any())->method('isValidationRequired')->willReturn(true);
        $objectManager = Bootstrap::getObjectManager();
        $this->_model = $objectManager->create(
            Reader::class,
            ['fileResolver' => $fileResolverMock, 'validationState' => $validationStateMock]
        );
    }
}
