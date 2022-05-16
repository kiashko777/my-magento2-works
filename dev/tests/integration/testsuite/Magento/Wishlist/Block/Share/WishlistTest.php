<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Wishlist\Block\Share;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class WishlistTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Wishlist
     */
    protected $_block;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Wishlist/_files/wishlist.php
     */
    public function testGetWishlistCustomer()
    {
        $this->_customerSession->loginById(1);
        $expectedCustomer = $this->_customerSession->getCustomerDataObject();
        $actualCustomer = $this->_block->getWishlistCustomer();
        $this->assertInstanceOf(CustomerInterface::class, $actualCustomer);
        $this->assertEquals((int)$expectedCustomer->getId(), (int)$actualCustomer->getId());
        $this->assertEquals((int)$expectedCustomer->getWebsiteId(), (int)$actualCustomer->getWebsiteId());
        $this->assertEquals((int)$expectedCustomer->getStoreId(), (int)$actualCustomer->getStoreId());
        $this->assertEquals((int)$expectedCustomer->getGroupId(), (int)$actualCustomer->getGroupId());
        $this->assertEquals($expectedCustomer->getCustomAttributes(), $actualCustomer->getCustomAttributes());
        $this->assertEquals($expectedCustomer->getFirstname(), $actualCustomer->getFirstname());
        $this->assertEquals($expectedCustomer->getLastname(), $actualCustomer->getLastname());
        $this->assertEquals($expectedCustomer->getEmail(), $actualCustomer->getEmail());
        $this->assertEquals($expectedCustomer->getEmail(), $actualCustomer->getEmail());
        $this->assertEquals((int)$expectedCustomer->getDefaultBilling(), (int)$actualCustomer->getDefaultBilling());
        $this->assertEquals((int)$expectedCustomer->getDefaultShipping(), (int)$actualCustomer->getDefaultShipping());
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->_customerSession = $this->_objectManager->get(Session::class);
        $this->_block = $this->_objectManager->create(Wishlist::class);
    }
}
