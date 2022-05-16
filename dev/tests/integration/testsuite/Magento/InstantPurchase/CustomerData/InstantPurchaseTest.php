<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\InstantPurchase\CustomerData;

use Magento\Customer\Model\Session;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 */
class InstantPurchaseTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/InstantPurchase/_files/fake_payment_token.php
     */
    public function testDefaultFormatterIsAppliedWhenBasicIntegration()
    {
        /** @var Session $customerSession */
        $customerSession = $this->objectManager->get(Session::class);
        $customerSession->loginById(1);

        /** @var InstantPurchase $customerDataSectionSource */
        $customerDataSectionSource = $this->objectManager->get(InstantPurchase::class);
        $data = $customerDataSectionSource->getSectionData();
        $this->assertEquals(
            'Fake Payment Method Vault',
            $data['paymentToken']['summary'],
            'Basic implementation returns provider title.'
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/InstantPurchase/_files/fake_payment_token.php
     * @magentoConfigFixture current_store payment/fake_vault/instant_purchase/tokenFormat StubFormatter
     */
    public function testCustomFormatterIsAppliedWhenComplexIntegration()
    {
        $this->objectManager->configure([
            'StubFormatter' => [
                'type' => StubPaymentTokenFormatter::class,
            ],
        ]);
        /** @var Session $customerSession */
        $customerSession = $this->objectManager->get(Session::class);
        $customerSession->loginById(1);

        /** @var InstantPurchase $customerDataSectionSource */
        $customerDataSectionSource = $this->objectManager->get(InstantPurchase::class);
        $data = $customerDataSectionSource->getSectionData();
        $this->assertEquals(
            StubPaymentTokenFormatter::VALUE,
            $data['paymentToken']['summary'],
            'Complex implementation returns custom string.'
        );
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
