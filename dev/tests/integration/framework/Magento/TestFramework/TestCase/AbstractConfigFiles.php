<?php
/**
 * Abstract class that helps in writing tests that validate config xml files
 * are valid both individually and when merged.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\TestCase;

use Magento\Framework\App\Arguments\FileResolver\Primary;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\DirSearch;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\FileIterator;
use Magento\Framework\Config\FileIteratorFactory;
use Magento\Framework\Config\Reader\Filesystem;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractConfigFiles extends TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    /**
     * @var  Filesystem
     */
    protected $_reader;

    /**
     * @var MockObject
     */
    protected $_fileResolverMock;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @dataProvider xmlConfigFileProvider
     */
    public function testXmlConfigFile($file, $skip = false)
    {
        if ($skip) {
            $this->markTestSkipped('There are no xml files in the system for this test.');
        }
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->method('isValidationRequired')
            ->willReturn(false);
        $domConfig = new Dom($file, $validationStateMock);
        $errors = [];
        $result = $domConfig->validate($this->_schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error}\n";
        }

        $this->assertTrue($result, $message);
    }

    public function testMergedConfig()
    {
        $files = $this->getXmlConfigFiles();
        if (empty($files)) {
            $this->markTestSkipped('There are no xml files in the system for this test.');
        }
        // have the file resolver return all relevant xml files
        $this->_fileResolverMock->expects($this->any())
            ->method('get')
            ->willReturn($this->getXmlConfigFiles());

        try {
            // this will merge all xml files and validate them
            $this->_reader->read('global');
        } catch (LocalizedException $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Returns an array of all the config xml files for this test.
     *
     * Handles the case where no files were found and notifies the test to skip.
     * This is needed to avoid a fatal error caused by a provider returning an empty array.
     *
     * @return array
     */
    public function xmlConfigFileProvider()
    {
        $fileList = $this->getXmlConfigFiles();
        $result = [];
        foreach ($fileList as $fileContent) {
            $result[] = [$fileContent];
        }
        return $result;
    }

    protected function setUp(): void
    {
        $this->componentRegistrar = new ComponentRegistrar();
        $this->_objectManager = Bootstrap::getObjectManager();
        $xmlFiles = $this->getXmlConfigFiles();
        if (!empty($xmlFiles)) {
            $this->_fileResolverMock = $this->getMockBuilder(
                Primary::class
            )->disableOriginalConstructor()->getMock();

            /* Enable Validation regardless of MAGE_MODE */
            $validateStateMock = $this->getMockBuilder(
                ValidationStateInterface::class
            )->disableOriginalConstructor()->getMock();
            $validateStateMock->expects($this->any())->method('isValidationRequired')->willReturn(true);

            $this->_reader = $this->_objectManager->create(
                $this->_getReaderClassName(),
                [
                    'configFiles' => $xmlFiles,
                    'fileResolver' => $this->_fileResolverMock,
                    'validationState' => $validateStateMock
                ]
            );

            $this->_schemaFile = $this->_getXsdPath();
        }
    }

    /**
     * Finds all config xml files based on a path glob.
     *
     * @return FileIterator
     */
    public function getXmlConfigFiles()
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var $moduleDirSearch DirSearch */
        $moduleDirSearch = $objectManager->get(DirSearch::class);

        return $objectManager->get(FileIteratorFactory::class)
            ->create($moduleDirSearch->collectFiles(ComponentRegistrar::MODULE, $this->_getConfigFilePathGlob()));
    }

    /**
     * Returns a string that represents the path to the config file, starting in the app directory.
     *
     * Format is glob, so * is allowed.
     *
     * @return string
     */
    abstract protected function _getConfigFilePathGlob();

    /**
     * Returns the reader class name that will be instantiated via ObjectManager
     *
     * @return string reader class name
     */
    abstract protected function _getReaderClassName();

    /**
     * Returns an absolute path to the XSD file corresponding to the XML files specified in _getConfigFilePathGlob
     *
     * @return string
     */
    abstract protected function _getXsdPath();

    protected function tearDown(): void
    {
        $this->_objectManager->removeSharedInstance($this->_getReaderClassName());
    }
}
