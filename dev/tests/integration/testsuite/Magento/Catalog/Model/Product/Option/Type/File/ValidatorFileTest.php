<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product\Option\Type\File;

use Exception;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Size;
use Magento\Framework\Filesystem;
use Magento\Framework\HTTP\Adapter\FileTransferFactory;
use Magento\Framework\Math\Random;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Zend_File_Transfer_Adapter_Http;
use Zend_Validate_File_ExcludeExtension;
use Zend_Validate_File_Extension;
use Zend_Validate_File_FilesSize;
use Zend_Validate_File_ImageSize;

/**
 * @magentoDataFixture Magento/Catalog/_files/validate_image.php
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ValidatorFileTest extends TestCase
{
    /**
     * @var ValidatorFile
     */
    protected $model;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var FileTransferFactory|MockObject
     */
    protected $httpFactoryMock;

    /**
     * @var int
     */
    protected $maxFileSizeInMb;

    /**
     * @var int
     */
    protected $maxFileSize;

    /**
     * @return void
     */
    public function testRunValidationException()
    {
        $this->expectException(\Magento\Framework\Validator\Exception::class);

        $httpAdapterMock = $this->createPartialMock(Zend_File_Transfer_Adapter_Http::class, ['isValid']);
        $this->httpFactoryMock->expects($this->once())->method('create')->willReturn($httpAdapterMock);

        $this->model->validate(
            $this->objectManager->create(DataObject::class),
            $this->getProductOption(['is_require' => false])
        );
    }

    /**
     * @param array $options
     * @return Option
     */
    protected function getProductOption(array $options = [])
    {
        $data = [
            'option_id' => '1',
            'product_id' => '4',
            'type' => 'file',
            'is_require' => '1',
            'sku' => null,
            'max_characters' => null,
            'file_extension' => null,
            'image_size_x' => '2000',
            'image_size_y' => '2000',
            'sort_order' => '0',
            'default_title' => 'MediaOption',
            'store_title' => null,
            'title' => 'MediaOption',
            'default_price' => '5.0000',
            'default_price_type' => 'fixed',
            'store_price' => null,
            'store_price_type' => null,
            'price' => '5.0000',
            'price_type' => 'fixed',
        ];
        $option = $this->objectManager->create(
            Option::class,
            [
                'data' => array_merge($data, $options)
            ]
        );

        return $option;
    }

    /**
     * @backupGlobals enabled
     * @return void
     */
    public function testLargeSizeFile()
    {
        $this->expectException(LocalizedException::class);
        $exceptionMessage = 'The file was too big and couldn\'t be uploaded. Use a file smaller than %s MBs and try ' .
            'to upload again.';
        $this->expectExceptionMessage(
            sprintf($exceptionMessage, $this->maxFileSizeInMb)
        );
        $this->prepareEnv();
        $_SERVER['CONTENT_LENGTH'] = $this->maxFileSize + 1;
        $httpAdapterMock = $this->createPartialMock(Zend_File_Transfer_Adapter_Http::class, ['getFileInfo']);
        $exception = function () {
            throw new Exception();
        };
        $httpAdapterMock->expects($this->once())->method('getFileInfo')->willReturnCallback($exception);
        $this->httpFactoryMock->expects($this->once())->method('create')->willReturn($httpAdapterMock);

        $property = new ReflectionProperty($httpAdapterMock, '_files');
        $property->setAccessible(true);
        $property->setValue($httpAdapterMock, ['options_1_file' => $_FILES['options_1_file']]);
        $this->model->validate(
            $this->objectManager->create(DataObject::class),
            $this->getProductOption(['is_require' => false])
        );
    }

    /**
     * @return void
     */
    protected function prepareEnv()
    {
        $file = 'magento_small_image.jpg';

        /** @var Filesystem $filesystem */
        $filesystem = $this->objectManager->get(Filesystem::class);
        $tmpDirectory = $filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
        $filePath = $tmpDirectory->getAbsolutePath($file);

        $_FILES['options_1_file'] = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $filePath,
            'error' => 0,
            'size' => 12500,
        ];
    }

    /**
     * @return void
     */
    public function testOptionRequiredException()
    {
        $this->expectException(\Magento\Catalog\Model\Product\Exception::class);

        $this->prepareEnv();
        $httpAdapterMock = $this->createPartialMock(Zend_File_Transfer_Adapter_Http::class, ['getFileInfo']);
        $exception = function () {
            throw new Exception();
        };
        $httpAdapterMock->expects($this->once())->method('getFileInfo')->willReturnCallback($exception);
        $this->httpFactoryMock->expects($this->once())->method('create')->willReturn($httpAdapterMock);

        $property = new ReflectionProperty($httpAdapterMock, '_files');
        $property->setAccessible(true);
        $property->setValue($httpAdapterMock, ['options_1_file' => $_FILES['options_1_file']]);
        $this->model->validate(
            $this->objectManager->create(DataObject::class),
            $this->getProductOption(['is_require' => false])
        );
    }

    /**
     * @return void
     */
    public function testException()
    {
        $this->expectException(LocalizedException::class);

        $this->prepareEnv();
        $httpAdapterMock = $this->createPartialMock(Zend_File_Transfer_Adapter_Http::class, ['isUploaded']);
        $httpAdapterMock->expects($this->once())->method('isUploaded')->willReturn(false);
        $this->httpFactoryMock->expects($this->once())->method('create')->willReturn($httpAdapterMock);

        $property = new ReflectionProperty($httpAdapterMock, '_files');
        $property->setAccessible(true);
        $property->setValue($httpAdapterMock, ['options_1_file' => $_FILES['options_1_file']]);
        $this->model->validate(
            $this->objectManager->create(DataObject::class),
            $this->getProductOption()
        );

        $this->expectExceptionMessage(
            "The product's required option(s) weren't entered. Make sure the options are entered and try again."
        );
    }

    /**
     * @return void
     */
    public function testInvalidateFile()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage(
            "The file 'test.jpg' for 'MediaOption' has an invalid extension.\n"
            . "The file 'test.jpg' for 'MediaOption' has an invalid extension.\n"
            . "The maximum allowed image size for 'MediaOption' is 2000x2000 px.\n"
            . sprintf(
                "The file 'test.jpg' you uploaded is larger than the %s megabytes allowed by our server.",
                $this->maxFileSizeInMb
            )
        );
        $this->prepareEnv();
        $httpAdapterMock = $this->createPartialMock(
            Zend_File_Transfer_Adapter_Http::class,
            ['isValid', 'getErrors', 'getFileInfo', 'isUploaded']
        );
        $httpAdapterMock->expects($this->once())
            ->method('getFileInfo')
            ->willReturn([
                'options_1_file' => [
                    'name' => 'test.jpg'
                ]
            ]);
        $httpAdapterMock->expects($this->once())
            ->method('isValid')
            ->willReturn(false);
        $httpAdapterMock->expects($this->exactly(2))
            ->method('getErrors')
            ->willReturn(
                [
                    Zend_Validate_File_ExcludeExtension::FALSE_EXTENSION,
                    Zend_Validate_File_Extension::FALSE_EXTENSION,
                    Zend_Validate_File_ImageSize::WIDTH_TOO_BIG,
                    Zend_Validate_File_FilesSize::TOO_BIG,
                ]
            );
        $this->httpFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($httpAdapterMock);
        $httpAdapterMock->expects($this->once())
            ->method('isUploaded')
            ->willReturn(true);
        $this->model->validate(
            $this->objectManager->create(DataObject::class),
            $this->getProductOption()
        );
    }

    /**
     * @return void
     */
    public function testValidate()
    {
        $this->prepareGoodEnv();
        $httpAdapterMock = $this->createPartialMock(Zend_File_Transfer_Adapter_Http::class, ['isValid']);
        $httpAdapterMock->expects($this->once())->method('isValid')->willReturn(true);
        $this->httpFactoryMock->expects($this->once())->method('create')->willReturn($httpAdapterMock);

        $property = new ReflectionProperty($httpAdapterMock, '_files');
        $property->setAccessible(true);
        $property->setValue($httpAdapterMock, ['options_1_file' => $_FILES['options_1_file']]);
        $result = $this->model->validate(
            $this->objectManager->create(DataObject::class),
            $this->getProductOption()
        );
        unset($result['fullpath'], $result['secret_key']);
        $this->assertEquals($this->expectedValidate(), $result);
    }

    /**
     * @return void
     */
    protected function prepareGoodEnv()
    {
        $file = 'magento_small_image.jpg';

        /** @var Filesystem $filesystem */
        $filesystem = $this->objectManager->get(Filesystem::class);
        $tmpDirectory = $filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
        $filePath = $tmpDirectory->getAbsolutePath($file);

        $_FILES['options_1_file'] = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $filePath,
            'error' => 0,
            'size' => '3046',
        ];
    }

    /**
     * @return array
     */
    protected function expectedValidate()
    {
        return [
            'type' => 'image/jpeg',
            'title' => 'test.jpg',
            'quote_path' => 'custom_options/quote/R/a/RandomString',
            'order_path' => 'custom_options/order/R/a/RandomString',
            'size' => '3046',
            'width' => 136,
            'height' => 131,
        ];
    }

    public function testEmptyFile()
    {
        $this->prepareEnvForEmptyFile();

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('The file is empty. Select another file and try again.');

        $httpAdapterMock = $this->createPartialMock(Zend_File_Transfer_Adapter_Http::class, ['isValid']);
        $httpAdapterMock->expects($this->once())->method('isValid')->willReturn(true);
        $this->httpFactoryMock->expects($this->once())->method('create')->willReturn($httpAdapterMock);

        $property = new ReflectionProperty($httpAdapterMock, '_files');
        $property->setAccessible(true);
        $property->setValue($httpAdapterMock, ['options_1_file' => $_FILES['options_1_file']]);
        $this->model->validate(
            $this->objectManager->create(DataObject::class),
            $this->getProductOption()
        );
    }

    /**
     * Test exception for empty file
     *
     * @return void
     */
    protected function prepareEnvForEmptyFile()
    {
        $file = 'magento_empty.jpg';

        /** @var Filesystem $filesystem */
        $filesystem = $this->objectManager->get(Filesystem::class);
        $tmpDirectory = $filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
        $filePath = $tmpDirectory->getAbsolutePath($file);

        $_FILES['options_1_file'] = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $filePath,
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->httpFactoryMock = $this->createPartialMock(
            FileTransferFactory::class,
            ['create']
        );
        /** @var Size $fileSize */
        $fileSize = $this->objectManager->create(Size::class);
        $this->maxFileSize = $fileSize->getMaxFileSize();
        $this->maxFileSizeInMb = $fileSize->getMaxFileSizeInMb();
        $random = $this->getMockBuilder(Random::class)
            ->disableOriginalConstructor()
            ->getMock();
        $random->expects($this->any())
            ->method('getRandomString')
            ->willReturn('RandomString');

        $this->model = $this->objectManager->create(
            ValidatorFile::class,
            [
                'httpFactory' => $this->httpFactoryMock,
                'random' => $random,
            ]
        );
    }
}
