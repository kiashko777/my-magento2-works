<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\DirSearch;
use Magento\Framework\Config\FileIteratorFactory;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\ImportExport\Model\Import\Config\Reader;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ImportConfigFilesTest extends TestCase
{
    /**
     * @var Reader
     */
    protected $_model;

    public function testImportXmlFiles()
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
            $moduleDirSearch->collectFiles(ComponentRegistrar::MODULE, 'etc/{*/import.xml,import.xml}')
        );

        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->expects($this->any())->method('isValidationRequired')->willReturn(true);
        $fileResolverMock = $this->createMock(FileResolverInterface::class);
        $fileResolverMock->expects($this->any())->method('get')->willReturn($xmlFiles);
        $objectManager = Bootstrap::getObjectManager();

        $this->_model = $objectManager->create(
            Reader::class,
            ['fileResolver' => $fileResolverMock, 'validationState' => $validationStateMock]
        );
    }
}
