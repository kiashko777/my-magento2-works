<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Helper;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /** @var View */
    protected $_helper;

    /** @var CustomerMetadataInterface|MockObject */
    protected $_customerMetadataService;

    /**
     * @param CustomerInterface $customerData
     * @param string $expectedCustomerName
     * @param bool $isPrefixAllowed
     * @param bool $isMiddleNameAllowed
     * @param bool $isSuffixAllowed
     * @dataProvider getCustomerNameDataProvider
     */
    public function testGetCustomerName(
        $customerData,
        $expectedCustomerName,
        $isPrefixAllowed = false,
        $isMiddleNameAllowed = false,
        $isSuffixAllowed = false
    )
    {
        $visibleAttribute = $this->createMock(AttributeMetadataInterface::class);
        $visibleAttribute->expects($this->any())->method('isVisible')->willReturn(true);

        $invisibleAttribute = $this->createMock(AttributeMetadataInterface::class);
        $invisibleAttribute->expects($this->any())->method('isVisible')->willReturn(false);

        $this->_customerMetadataService->expects(
            $this->any()
        )->method(
            'getAttributeMetadata'
        )->willReturnMap(
            [
                ['prefix', $isPrefixAllowed ? $visibleAttribute : $invisibleAttribute],
                ['middlename', $isMiddleNameAllowed ? $visibleAttribute : $invisibleAttribute],
                ['suffix', $isSuffixAllowed ? $visibleAttribute : $invisibleAttribute],
            ]
        );

        $this->assertEquals(
            $expectedCustomerName,
            $this->_helper->getCustomerName($customerData),
            'Full customer name is invalid'
        );
    }

    public function getCustomerNameDataProvider()
    {
        /** @var CustomerInterfaceFactory $customerFactory */
        $customerFactory = Bootstrap::getObjectManager()->create(
            CustomerInterfaceFactory::class
        );
        return [
            'With disabled prefix, middle name, suffix' => [
                $customerFactory->create()->setPrefix(
                    'prefix'
                )->setFirstname(
                    'FirstName'
                )->setMiddlename(
                    'MiddleName'
                )->setLastname(
                    'LastName'
                )->setSuffix(
                    'suffix'
                ),
                'FirstName LastName',
            ],
            'With prefix, middle name, suffix' => [
                $customerFactory->create()->setPrefix(
                    'prefix'
                )->setFirstname(
                    'FirstName'
                )->setMiddlename(
                    'MiddleName'
                )->setLastname(
                    'LastName'
                )->setSuffix(
                    'suffix'
                ),
                'prefix FirstName MiddleName LastName suffix',
                true, // $isPrefixAllowed
                true, // $isMiddleNameAllowed
                true, //$isSuffixAllowed
            ],
            'Empty prefix, middle name, suffix' => [
                $customerFactory->create()->setFirstname('FirstName')->setLastname('LastName'),
                'FirstName LastName',
                true, // $isPrefixAllowed
                true, // $isMiddleNameAllowed
                true, //$isSuffixAllowed
            ],
            'Empty prefix and suffix, not empty middle name' => [
                $customerFactory->create()->setFirstname(
                    'FirstName'
                )->setMiddlename(
                    'MiddleName'
                )->setLastname(
                    'LastName'
                ),
                'FirstName MiddleName LastName',
                true, // $isPrefixAllowed
                true, // $isMiddleNameAllowed
                true, //$isSuffixAllowed
            ],
            'With html entities' => [
                $customerFactory->create()->setPrefix(
                    'prefix'
                )->setFirstname(
                    '<h1>FirstName</h1>'
                )->setLastname(
                    '<strong>LastName</strong>'
                ),
                '&lt;h1&gt;FirstName&lt;/h1&gt; &lt;strong&gt;LastName&lt;/strong&gt;',
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->_customerMetadataService = $this->createMock(CustomerMetadataInterface::class);
        $this->_helper = Bootstrap::getObjectManager()->create(
            View::class,
            ['customerMetadataService' => $this->_customerMetadataService]
        );
        parent::setUp();
    }
}
