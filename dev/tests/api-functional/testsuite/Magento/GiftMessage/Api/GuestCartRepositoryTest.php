<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GiftMessage\Api;

use Magento\Framework\Webapi\Rest\Request;
use Magento\GiftMessage\Model\Message;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\WebapiAbstract;

class GuestCartRepositoryTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';
    const SERVICE_NAME = 'giftMessageGuestCartRepositoryV1';
    const RESOURCE_PATH = '/V1/guest-carts/';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @magentoApiDataFixture Magento/GiftMessage/_files/quote_with_message.php
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     */
    public function testGet()
    {
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('message_order_21', 'reserved_order_id');
        $cartId = $quote->getId();
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = Bootstrap::getObjectManager()
            ->create(QuoteIdMaskFactory::class)
            ->create();
        $quoteIdMask->load($cartId, 'quote_id');
        //Use masked cart Id
        $cartId = $quoteIdMask->getMaskedId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/gift-message',
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];

        $expectedMessage = [
            'recipient' => 'Mercutio',
            'sender' => 'Romeo',
            'message' => 'I thought all for the best.',
        ];

        $requestData = ["cartId" => $cartId];
        $resultMessage = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertCount(5, $resultMessage);
        unset($resultMessage['gift_message_id']);
        unset($resultMessage['customer_id']);
        $this->assertEquals($expectedMessage, $resultMessage);
    }

    /**
     * @magentoApiDataFixture Magento/GiftMessage/_files/quote_with_item_message.php
     */
    public function testSave()
    {
        // sales/gift_options/allow_order must be set to 1 in system configuration
        // @todo remove next statement when \Magento\TestFramework\TestCase\WebapiAbstract::_updateAppConfig is fixed
        $this->markTestIncomplete('This test relies on system configuration state.');
        /** @var Quote $quote */
        $quote = $this->objectManager->create(Quote::class);
        $quote->load('test_order_item_with_message', 'reserved_order_id');
        $cartId = $quote->getId();
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = Bootstrap::getObjectManager()
            ->create(QuoteIdMaskFactory::class)
            ->create();
        $quoteIdMask->load($cartId, 'quote_id');
        //Use masked cart Id
        $cartId = $quoteIdMask->getMaskedId();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $cartId . '/gift-message',
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];

        $requestData = [
            'cartId' => $cartId,
            'giftMessage' => [
                'recipient' => 'John Doe',
                'sender' => 'Jane Roe',
                'message' => 'Gift Message Text New',
            ],
        ];
        $this->assertTrue($this->_webApiCall($serviceInfo, $requestData));
        $quote->load('test_order_item_with_message', 'reserved_order_id');
        $quote->getGiftMessageId();
        /** @var  Message $message */
        $message = $this->objectManager->create(Message::class)
            ->load($quote->getGiftMessageId());
        $this->assertEquals('John Doe', $message->getRecipient());
        $this->assertEquals('Jane Roe', $message->getSender());
        $this->assertEquals('Gift Message Text New', $message->getMessage());
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
