<?php
/**
 * Test for \Magento\Customer\Model\AddressRegistry
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class AddressRegistryTest extends TestCase
{
    /**
     * @var AddressRegistry
     */
    protected $_model;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testRetrieve()
    {
        $addressId = 1;
        $address = $this->_model->retrieve($addressId);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($addressId, $address->getId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testRetrieveCached()
    {
        $addressId = 1;
        $addressBeforeDeletion = $this->_model->retrieve($addressId);
        $address2 = Bootstrap::getObjectManager()
            ->create(Address::class);
        $address2->load($addressId)
            ->delete();
        $addressAfterDeletion = $this->_model->retrieve($addressId);
        $this->assertEquals($addressBeforeDeletion, $addressAfterDeletion);
        $this->assertInstanceOf(Address::class, $addressAfterDeletion);
        $this->assertEquals($addressId, $addressAfterDeletion->getId());
    }

    /**
     */
    public function testRetrieveException()
    {
        $this->expectException(NoSuchEntityException::class);

        $addressId = 1;
        $this->_model->retrieve($addressId);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testRemove()
    {
        $this->expectException(NoSuchEntityException::class);

        $addressId = 1;
        $address = $this->_model->retrieve($addressId);
        $this->assertInstanceOf(Address::class, $address);
        $address->delete();
        $this->_model->remove($addressId);
        $this->_model->retrieve($addressId);
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()
            ->create(AddressRegistry::class);
    }
}
