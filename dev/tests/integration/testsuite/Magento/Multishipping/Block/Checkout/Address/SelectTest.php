<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Multishipping\Block\Checkout\Address;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class SelectTest extends TestCase
{
    /** @var Select */
    protected $_selectBlock;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     */
    public function testGetAddressAsHtml()
    {
        /** @var AddressRepositoryInterface $addressRepository */
        $addressRepository = Bootstrap::getObjectManager()->create(
            AddressRepositoryInterface::class
        );
        $fixtureAddressId = 1;
        $address = $addressRepository->getById($fixtureAddressId);
        $addressAsHtml = $this->_selectBlock->getAddressAsHtml($address);
        $this->assertEquals(
            "John Smith<br />CompanyName<br />Green str, 67<br />CityM,  Alabama, 75477"
            . "<br />United States<br />T: <a href=\"tel:3468676\">3468676</a>",
            str_replace("\n", '', $addressAsHtml),
            "Address was represented as HTML incorrectly"
        );
    }

    protected function setUp(): void
    {
        $this->_selectBlock = Bootstrap::getObjectManager()->create(
            Select::class
        );
        parent::setUp();
    }
}
