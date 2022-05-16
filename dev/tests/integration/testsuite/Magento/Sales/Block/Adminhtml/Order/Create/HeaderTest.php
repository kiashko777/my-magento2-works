<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create;

use Magento\Backend\Model\Session\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class HeaderTest extends TestCase
{
    /** @var Header */
    protected $_block;

    /**
     * @param int|null $customerId
     * @param int|null $storeId
     * @param string $expectedResult
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @dataProvider toHtmlDataProvider
     */
    public function testToHtml($customerId, $storeId, $expectedResult)
    {
        /** @var Quote $session */
        $session = Bootstrap::getObjectManager()->get(Quote::class);
        $session->setCustomerId($customerId);
        $session->setStoreId($storeId);
        $this->assertEquals($expectedResult, $this->_block->toHtml());
    }

    public function toHtmlDataProvider()
    {
        $customerIdFromFixture = 1;
        $defaultStoreView = 1;
        return [
            'Customer and store' => [
                $customerIdFromFixture,
                $defaultStoreView,
                'Create New Order for John Smith in Default Store View',
            ],
            'No store' => [$customerIdFromFixture, null, 'Create New Order for John Smith'],
            'No customer' => [null, $defaultStoreView, 'Create New Order in Default Store View'],
            'No customer, no store' => [null, null, 'Create New Order for New Customer']
        ];
    }

    protected function setUp(): void
    {
        $this->_block = Bootstrap::getObjectManager()->create(
            Header::class
        );
        parent::setUp();
    }
}
