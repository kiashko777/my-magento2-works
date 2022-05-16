<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular\Magento\Sales;

use Exception;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Sales\Model\Order\Pdf\Config\Reader;
use Magento\Sales\Model\Order\Pdf\Config\SchemaLocator;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class PdfConfigFilesTest extends TestCase
{
    /**
     * @param string $file
     * @dataProvider fileFormatDataProvider
     */
    public function testFileFormat($file)
    {
        /** @var SchemaLocator $schemaLocator */
        $schemaLocator = Bootstrap::getObjectManager()->get(
            SchemaLocator::class
        );
        $schemaFile = $schemaLocator->getPerFileSchema();

        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->method('isValidationRequired')
            ->willReturn(true);
        $dom = new Dom(file_get_contents($file), $validationStateMock);
        $result = $dom->validate($schemaFile, $errors);
        $this->assertTrue($result, print_r($errors, true));
    }

    /**
     * @return array
     */
    public function fileFormatDataProvider()
    {
        return Files::init()->getConfigFiles('pdf.xml');
    }

    public function testMergedFormat()
    {
        $validationState = $this->createMock(ValidationStateInterface::class);
        $validationState->expects($this->any())->method('isValidationRequired')->willReturn(true);

        /** @var Reader $reader */
        $reader = Bootstrap::getObjectManager()->create(
            Reader::class,
            ['validationState' => $validationState]
        );
        try {
            $reader->read();
        } catch (Exception $e) {
            $this->fail('Merged pdf.xml files do not pass XSD validation: ' . $e->getMessage());
        }
    }
}
