<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use DOMDocument;
use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Config\FileIteratorFactory;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\ObjectManager\Config\Reader\Dom;
use Magento\Framework\ObjectManager\Config\SchemaLocator;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DiConfigFilesTest extends TestCase
{
    /**
     * Primary DI configs from app/etc
     * @var array
     */
    protected static $_primaryFiles = [];

    /**
     * Global DI configs from all modules
     * @var array
     */
    protected static $_moduleGlobalFiles = [];

    /**
     * Area DI configs from all modules
     * @var array
     */
    protected static $_moduleAreaFiles = [];

    /**
     * @param string $filePath
     * @param string $xml
     * @throws Exception
     * @dataProvider linearFilesProvider
     */
    public function testDiConfigFileWithoutMerging($filePath, $xml)
    {
        /** @var SchemaLocator $schemaLocator */
        $schemaLocator = Bootstrap::getObjectManager()->get(
            SchemaLocator::class
        );

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        libxml_use_internal_errors(true);
        $result = \Magento\Framework\Config\Dom::validateDomDocument($dom, $schemaLocator->getSchema());
        libxml_use_internal_errors(false);

        if (!empty($result)) {
            $this->fail(
                'File ' . $filePath . ' has invalid xml structure. '
                . implode("\n", $result)
            );
        }
    }

    public function linearFilesProvider()
    {
        if (empty(self::$_primaryFiles)) {
            $this->_prepareFiles();
        }

        $common = array_merge(self::$_primaryFiles->toArray(), self::$_moduleGlobalFiles->toArray());

        foreach (self::$_moduleAreaFiles as $files) {
            $common = array_merge($common, $files->toArray());
        }

        $output = [];
        foreach ($common as $path => $content) {
            $output[] = [substr($path, strlen(BP)), $content];
        }

        return $output;
    }

    protected function _prepareFiles()
    {
        //init primary configs
        $objectManager = Bootstrap::getObjectManager();
        /** @var $filesystem Filesystem */
        $filesystem = $objectManager->get(Filesystem::class);
        $configDirectory = $filesystem->getDirectoryRead(DirectoryList::CONFIG);
        $fileIteratorFactory = $objectManager->get(FileIteratorFactory::class);
        $search = [];
        foreach ($configDirectory->search('{*/di.xml,di.xml}') as $path) {
            $search[] = $configDirectory->getAbsolutePath($path);
        }
        self::$_primaryFiles = $fileIteratorFactory->create($search);
        //init module global configs
        /** @var $modulesReader Reader */
        $modulesReader = Bootstrap::getObjectManager()
            ->get(Reader::class);
        self::$_moduleGlobalFiles = $modulesReader->getConfigurationFiles('di.xml');

        //init module area configs
        $areas = ['Adminhtml', 'frontend'];
        foreach ($areas as $area) {
            $moduleAreaFiles = $modulesReader->getConfigurationFiles($area . '/di.xml');
            self::$_moduleAreaFiles[$area] = $moduleAreaFiles;
        }
    }

    /**
     * @param array $files
     * @dataProvider mixedFilesProvider
     */
    public function testMergedDiConfig(array $files)
    {
        $mapperMock = $this->createMock(\Magento\Framework\ObjectManager\Config\Mapper\Dom::class);
        $fileResolverMock = $this->getMockBuilder(FileResolverInterface::class)
            ->setMethods(['read'])
            ->getMockForAbstractClass();
        $fileResolverMock->expects($this->any())->method('read')->willReturn($files);
        $validationStateMock = $this->createPartialMock(
            ValidationStateInterface::class,
            ['isValidationRequired']
        );
        $validationStateMock->expects($this->any())->method('isValidationRequired')->willReturn(true);

        /** @var SchemaLocator $schemaLocator */
        $schemaLocator = Bootstrap::getObjectManager()->get(
            SchemaLocator::class
        );

        new Dom(
            $fileResolverMock,
            $mapperMock,
            $schemaLocator,
            $validationStateMock
        );
    }

    public function mixedFilesProvider()
    {
        if (empty(self::$_primaryFiles)) {
            $this->_prepareFiles();
        }
        foreach (self::$_primaryFiles->toArray() as $file) {
            $primaryFiles[] = [[$file]];
        }
        $primaryFiles['all primary config files'] = [self::$_primaryFiles->toArray()];

        foreach (self::$_moduleGlobalFiles->toArray() as $file) {
            $moduleFiles[] = [[$file]];
        }
        $moduleFiles['all module global config files'] = [self::$_moduleGlobalFiles->toArray()];

        $areaFiles = [];
        foreach (self::$_moduleAreaFiles as $area => $files) {
            foreach ($files->toArray() as $file) {
                $areaFiles[] = [[$file]];
            }
            $areaFiles["all {$area} config files"] = [self::$_moduleAreaFiles[$area]->toArray()];
        }

        return $primaryFiles + $moduleFiles + $areaFiles;
    }
}
