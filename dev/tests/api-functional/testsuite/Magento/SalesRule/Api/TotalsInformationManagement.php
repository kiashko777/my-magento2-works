<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SalesRule\Api;

use Magento\Framework\Registry;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Tests disabled cart rules for customer's cart
 */
class TotalsInformationManagement extends WebapiAbstract
{
    private const SERVICE_OPERATION = 'calculate';
    private const SERVICE_NAME = 'checkoutTotalsInformationManagementV1';
    private const SERVICE_VERSION = 'V1';
    private const RESOURCE_PATH = '/V1/carts/mine/totals-information';
    private const QUOTE_RESERVED_ORDER_ID = 'test01';
    private const SALES_RULE_ID = 'Magento/SalesRule/_files/cart_rule_50_percent_off_no_condition/salesRuleId';
    private const CUSTOMER_EMAIL = 'customer@example.com';
    private const CUSTOMER_PASSWORD = 'password';

    /**
     * Test sales rule changes should be persisted in the database
     *
     * @magentoApiDataFixture Magento/SalesRule/_files/cart_rule_50_percent_off_no_condition.php
     * @magentoApiDataFixture Magento/Sales/_files/quote_with_customer.php
     */
    public function testCalculate()
    {
        /** @var Quote $quote */
        /** @var QuoteIdMask $quoteIdMask */
        /** @var Rule $salesRule */
        /** @var Registry $registry */
        $registry = Bootstrap::getObjectManager()->get(Registry::class);
        $quote = Bootstrap::getObjectManager()->get(QuoteFactory::class)->create();
        $quote->load(self::QUOTE_RESERVED_ORDER_ID, 'reserved_order_id');
        $quoteIdMask = Bootstrap::getObjectManager()->get(QuoteIdMaskFactory::class)->create();
        $quoteIdMask->load($quote->getId(), 'quote_id');
        $salesRuleId = $registry->registry(self::SALES_RULE_ID);
        $salesRule = Bootstrap::getObjectManager()->create(RuleFactory::class)->create();
        $salesRule->load($salesRuleId);
        $this->assertContains($salesRule->getRuleId(), str_getcsv($quote->getAppliedRuleIds()));
        $salesRule->setIsActive(0);
        $salesRule->save();
        $response = $this->_webApiCall(
            [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH,
                    'httpMethod' => Request::HTTP_METHOD_POST,
                    'token' => Bootstrap::getObjectManager()
                        ->create(
                            CustomerTokenServiceInterface::class
                        )
                        ->createCustomerAccessToken(
                            self::CUSTOMER_EMAIL,
                            self::CUSTOMER_PASSWORD
                        )
                ],
                'soap' => [
                    'service' => self::SERVICE_NAME,
                    'serviceVersion' => self::SERVICE_VERSION,
                    'operation' => self::SERVICE_NAME . self::SERVICE_OPERATION,
                ],
            ],
            [
                'cartId' => $quote->getId(),
                'addressInformation' => [
                    'address' => []
                ]
            ]
        );
        $this->assertNotEmpty($response);
        $quote->load(self::QUOTE_RESERVED_ORDER_ID, 'reserved_order_id');
        $this->assertNotContains($salesRule->getId(), str_getcsv($quote->getAppliedRuleIds()));
    }
}
