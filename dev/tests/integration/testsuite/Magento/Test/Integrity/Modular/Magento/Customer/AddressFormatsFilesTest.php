<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular\Magento\Customer;

use Magento\Customer\Model\Address\Config\SchemaLocator;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AddressFormatsFilesTest extends TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    /**
     * @param string $file
     * @dataProvider fileFormatDataProvider
     */
    public function testFileFormat($file)
    {
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->method('isValidationRequired')
            ->willReturn(true);
        $dom = new Dom(file_get_contents($file), $validationStateMock);
        $result = $dom->validate($this->_schemaFile, $errors);
        $this->assertTrue($result, print_r($errors, true));
    }

    /**
     * @return array
     */
    public function fileFormatDataProvider()
    {
        return Files::init()->getConfigFiles(
            '{*/address_formats.xml,address_formats.xml}'
        );
    }

    protected function setUp(): void
    {
        /** @var SchemaLocator $schemaLocator */
        $schemaLocator = Bootstrap::getObjectManager()->get(
            SchemaLocator::class
        );
        $this->_schemaFile = $schemaLocator->getSchema();
    }
}
