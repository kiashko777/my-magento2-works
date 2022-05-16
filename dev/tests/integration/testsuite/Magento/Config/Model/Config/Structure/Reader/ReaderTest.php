<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Config\Model\Config\Structure\Reader;

use DOMDocument;
use Magento\Config\Model\Config\SchemaLocator;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\TemplateEngine\Xhtml\CompilerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ReaderTest check Magento\Config\Model\Config\Structure\Reader::_readFiles() method.
 */
class ReaderTest extends TestCase
{
    /**
     * Test config location.
     *
     * @string
     */
    const CONFIG = '/dev/tests/integration/testsuite/Magento/Config/Model/Config/Structure/Reader/_files/';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Files
     */
    private $fileUtility;

    /**
     * @var ValidationStateInterface
     */
    private $validationStateMock;

    /**
     * @var SchemaLocatorInterface
     */
    private $schemaLocatorMock;

    /**
     * @var FileResolverInterface
     */
    private $fileResolverMock;

    /**
     * @var ReaderStub
     */
    private $reader;

    /**
     * @var ConverterStub
     */
    private $converter;

    /**
     * @var CompilerInterface|MockObject
     */
    private $compiler;

    /**
     * The test checks the file structure after processing the nodes responsible for inserting content.
     *
     * @return void
     */
    public function testXmlConvertedConfigurationAndCompereStructure()
    {
        $actual = $this->reader->readFiles(['actual' => $this->getContent()]);

        $document = new DOMDocument();
        $document->loadXML($this->getContent());

        $expected = $this->converter->getArrayData($document);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Get config sample data for test.
     *
     * @return string
     */
    protected function getContent()
    {
        $files = $this->fileUtility->getFiles([BP . static::CONFIG], 'config.xml');

        return file_get_contents(reset($files));
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->fileUtility = Files::init();

        $this->validationStateMock = $this->getMockBuilder(ValidationStateInterface::class)
            ->setMethods(['isValidationRequired'])
            ->getMockForAbstractClass();
        $this->schemaLocatorMock = $this->getMockBuilder(SchemaLocator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPerFileSchema'])
            ->getMock();
        $this->fileResolverMock = $this->getMockBuilder(FileResolverInterface::class)
            ->getMockForAbstractClass();

        $this->validationStateMock->expects($this->atLeastOnce())
            ->method('isValidationRequired')
            ->willReturn(false);
        $this->schemaLocatorMock->expects($this->atLeastOnce())
            ->method('getPerFileSchema')
            ->willReturn(false);

        $this->converter = $this->objectManager->create(ConverterStub::class);

        //Isolate test from actual configuration, and leave only sample data.
        $this->compiler = $this->getMockBuilder(CompilerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['compile'])
            ->getMockForAbstractClass();

        $this->reader = $this->objectManager->create(
            ReaderStub::class,
            [
                'fileResolver' => $this->fileResolverMock,
                'converter' => $this->converter,
                'schemaLocator' => $this->schemaLocatorMock,
                'validationState' => $this->validationStateMock,
                'fileName' => 'no_existing_file.xml',
                'compiler' => $this->compiler,
                'domDocumentClass' => Dom::class
            ]
        );
    }
}
