<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Paypal\Model\Payment\Method\Billing;

use LogicException;
use Magento\Framework\DataObject;
use Magento\Payment\Model\Info;
use Magento\Paypal\Model\Config;
use Magento\Paypal\Model\Method\Agreement;
use Magento\Paypal\Model\Pro;
use Magento\Paypal\Model\ResourceModel\Billing\Agreement\Collection;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;

class AbstractAgreementTest extends TestCase
{
    /** @var Agreement */
    protected $_model;

    public static function setUpBeforeClass(): void
    {
        $db = Bootstrap::getInstance()->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        if (!$db->isDbDumpExists()) {
            throw new LogicException('DB dump does not exist.');
        }
        $db->restoreFromDbDump();

        parent::setUpBeforeClass();
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/quote_with_customer.php
     * @magentoDataFixture Magento/Paypal/_files/billing_agreement.php
     */
    public function testIsActive()
    {
        $quote = Bootstrap::getObjectManager()->create(
            \Magento\Quote\Model\ResourceModel\Quote\Collection::class
        )->getFirstItem();
        $this->assertTrue($this->_model->isAvailable($quote));
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/quote_with_customer.php
     * @magentoDataFixture Magento/Paypal/_files/billing_agreement.php
     */
    public function testAssignData()
    {
        /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection $collection */
        $collection = Bootstrap::getObjectManager()->create(
            \Magento\Quote\Model\ResourceModel\Quote\Collection::class
        );
        /** @var Quote $quote */
        $quote = $collection->getFirstItem();

        /** @var Info $info */
        $info = Bootstrap::getObjectManager()->create(
            Info::class
        )->setQuote(
            $quote
        );
        $this->_model->setData('info_instance', $info);
        $billingAgreement = Bootstrap::getObjectManager()->create(
            Collection::class
        )->getFirstItem();
        $data = new DataObject(
            [
                PaymentInterface::KEY_ADDITIONAL_DATA => [
                    AbstractAgreement::TRANSPORT_BILLING_AGREEMENT_ID => $billingAgreement->getId()
                ]
            ]
        );
        $this->_model->assignData($data);
        $this->assertEquals(
            'REF-ID-TEST-678',
            $info->getAdditionalInformation(AbstractAgreement::PAYMENT_INFO_REFERENCE_ID)
        );
    }

    protected function setUp(): void
    {
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->any())->method('isMethodAvailable')->willReturn(true);
        $proMock = $this->getMockBuilder(Pro::class)->disableOriginalConstructor()->getMock();
        $proMock->expects($this->any())->method('getConfig')->willReturn($config);
        $this->_model = Bootstrap::getObjectManager()->create(
            Agreement::class,
            ['data' => [$proMock]]
        );
    }

    /**
     * teardown
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
