<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Customer\Api;

use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\Api\Data\ImageFactory;
use Magento\Framework\Api\ImageContentFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\DenyListPathValidator;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Webapi\Exception as HTTPExceptionCodes;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Customer as CustomerHelper;
use Magento\TestFramework\TestCase\WebapiAbstract;
use SoapFault;

/**
 * Test class for Customer's custom attributes
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AccountManagementCustomAttributesTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'customerAccountManagementV1';
    const RESOURCE_PATH = '/V1/customers';

    /**
     * Sample values for testing
     */
    const ATTRIBUTE_CODE = 'attribute_code';
    const ATTRIBUTE_VALUE = 'attribute_value';

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * @var array
     */
    private $currentCustomerId;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @magentoApiDataFixture Magento/Customer/_files/attribute_user_defined_custom_attribute.php
     */
    public function testCreateCustomerWithImageAttribute()
    {
        $customerData = $this->createCustomerWithDefaultImageAttribute();
        $this->currentCustomerId[] = $customerData['id'];
        $this->verifyImageAttribute($customerData[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES], 'sample.jpeg');
    }

    /**
     * Create customer with a sample image file
     */
    protected function createCustomerWithDefaultImageAttribute()
    {
        $testImagePath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'test_image.jpg';
        $imageData = base64_encode(file_get_contents($testImagePath));
        $image = $this->imageFactory->create()
            ->setType('image/jpeg')
            ->setName('sample.jpeg')
            ->setBase64EncodedData($imageData);

        $imageData = $this->dataObjectProcessor->buildOutputDataArray(
            $image,
            ImageContentInterface::class
        );
        return $this->createCustomerWithImageAttribute($imageData);
    }

    /**
     * Create customer with image attribute
     *
     * @param array $imageData
     * @return array Customer data as array
     */
    protected function createCustomerWithImageAttribute($imageData)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'CreateAccount',
            ],
        ];

        $customerData = $this->customerHelper->createSampleCustomerDataObject();

        $customerDataArray = $this->dataObjectProcessor->buildOutputDataArray(
            $customerData,
            CustomerInterface::class
        );
        $customerDataArray['custom_attributes'][] = [
            'attribute_code' => 'customer_image',
            'value' => $imageData,
        ];
        $requestData = [
            'customer' => $customerDataArray,
            'password' => CustomerHelper::PASSWORD
        ];
        $customerData = $this->_webApiCall($serviceInfo, $requestData);

        return $customerData;
    }

    protected function verifyImageAttribute($customAttributeArray, $expectedFileName)
    {
        $imageAttributeFound = false;
        foreach ($customAttributeArray as $customAttribute) {
            if ($customAttribute[AttributeValue::ATTRIBUTE_CODE] == 'customer_image') {
                $this->assertStringContainsString($expectedFileName, $customAttribute[AttributeValue::VALUE]);
                $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
                $customerMediaPath = $mediaDirectory->getAbsolutePath(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER);
                $imageAttributeFound = file_exists($customerMediaPath . $customAttribute[AttributeValue::VALUE]);
                $this->assertTrue($imageAttributeFound, 'Expected file was not created');
            }
        }
        if (!$imageAttributeFound) {
            $this->fail('Expected image attribute missing.');
        }
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/attribute_user_defined_custom_attribute.php
     */
    public function testCreateCustomerWithInvalidImageAttribute()
    {
        $image = $this->imageFactory->create()
            ->setType('image/jpeg')
            ->setName('sample.jpeg')
            ->setBase64EncodedData('INVALID_IMAGE_DATA');

        $imageData = $this->dataObjectProcessor->buildOutputDataArray(
            $image,
            ImageContentInterface::class
        );
        $expectedMessage = 'The image content must be valid base64 encoded data.';
        try {
            $this->createCustomerWithImageAttribute($imageData);
        } catch (SoapFault $e) {
            $this->assertStringContainsString(
                $expectedMessage,
                $e->getMessage(),
                "Exception message does not match"
            );
        } catch (Exception $e) {
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals(HTTPExceptionCodes::HTTP_BAD_REQUEST, $e->getCode());
        }
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/attribute_user_defined_custom_attribute.php
     */
    public function testUpdateCustomerWithImageAttribute()
    {
        $customerDataArray = $this->createCustomerWithDefaultImageAttribute();
        $previousCustomerData = $customerDataArray;

        $testImagePath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'buttons.png';
        $imageData = base64_encode(file_get_contents($testImagePath));
        $image = $this->imageFactory->create()
            ->setType('image/png')
            ->setName('buttons.png')
            ->setBase64EncodedData($imageData);
        $imageData = $this->dataObjectProcessor->buildOutputDataArray(
            $image,
            ImageContentInterface::class
        );

        //Replace image attribute
        $customerDataArray['custom_attributes'][1] = [
            'attribute_code' => 'customer_image',
            'value' => $imageData,
        ];
        $requestData = [
            'customer' => $customerDataArray,
            'password' => CustomerHelper::PASSWORD
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . "/{$customerDataArray[CustomerInterface::ID]}",
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => 'customerCustomerRepositoryV1',
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => 'customerCustomerRepositoryV1Save',
            ],
        ];
        $customerData = $this->_webApiCall($serviceInfo, $requestData);
        $this->verifyImageAttribute($customerData[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES], 'buttons.png');

        //Verify that the previous image is deleted
        $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
        $customerMediaPath = $mediaDirectory->getAbsolutePath(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER);
        $previousImagePath =
            $previousCustomerData[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES][0][AttributeValue::VALUE];
        $this->assertFileDoesNotExist($customerMediaPath . $previousImagePath);
    }

    /**
     * Execute per test initialization.
     */
    protected function setUp(): void
    {
        $this->accountManagement = Bootstrap::getObjectManager()->get(
            AccountManagementInterface::class
        );

        $this->customerHelper = new CustomerHelper();

        $this->dataObjectProcessor = Bootstrap::getObjectManager()->create(
            DataObjectProcessor::class
        );

        $this->imageFactory = Bootstrap::getObjectManager()->get(ImageContentFactory::class);

        $this->fileSystem = Bootstrap::getObjectManager()->get(Filesystem::class);
    }

    protected function tearDown(): void
    {
        if (!empty($this->currentCustomerId)) {
            foreach ($this->currentCustomerId as $customerId) {
                $serviceInfo = [
                    'rest' => [
                        'resourcePath' => self::RESOURCE_PATH . '/' . $customerId,
                        'httpMethod' => Request::HTTP_METHOD_DELETE,
                    ],
                    'soap' => [
                        'service' => CustomerRepositoryTest::SERVICE_NAME,
                        'serviceVersion' => self::SERVICE_VERSION,
                        'operation' => CustomerRepositoryTest::SERVICE_NAME . 'DeleteById',
                    ],
                ];

                $response = $this->_webApiCall($serviceInfo, ['customerId' => $customerId]);

                $this->assertTrue($response);
            }
        }
        $this->accountManagement = null;
        $writeFactory = Bootstrap::getObjectManager()
            ->get(WriteFactory::class);
        $mediaDirectory = $writeFactory->create(DirectoryList::MEDIA);
        $denyListPathValidator = Bootstrap::getObjectManager()
            ->create(DenyListPathValidator::class, ['driver' => $mediaDirectory->getDriver()]);
        $denyListPathValidator->addException($mediaDirectory->getAbsolutePath() . ".htaccess");
        $writeFactoryBypassDenyList = Bootstrap::getObjectManager()
            ->create(WriteFactory::class, ['denyListPathValidator' => $denyListPathValidator]);
        $mediaDirectoryBypassDenyList = $writeFactoryBypassDenyList->create(DirectoryList::MEDIA);
        $mediaDirectoryBypassDenyList->delete(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER);
    }
}
