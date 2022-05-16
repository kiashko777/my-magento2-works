<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Sales\Model\ResourceModel\Order\Payment;

use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class EncryptionUpdateTest extends TestCase
{
    const TEST_CC_NUMBER = '4111111111111111';

    /**
     * Tests re-encryption of credit card numbers
     *
     * @magentoDataFixture Magento/Sales/_files/payment_enc_cc.php
     */
    public function testReEncryptCreditCardNumbers()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var EncryptorInterface $encyptor */
        $encyptor = $objectManager->get(EncryptorInterface::class);

        /** @var EncryptionUpdate $resource */
        $resource = $objectManager->create(EncryptionUpdate::class);
        $resource->reEncryptCreditCardNumbers();

        /** @var Collection $collection */
        $collection = $objectManager->create(Collection::class);
        $collection->addFieldToFilter('cc_number_enc', ['notnull' => true]);

        $this->assertGreaterThan(0, $collection->getTotalCount());

        /** @var Payment $payment */
        foreach ($collection->getItems() as $payment) {
            $this->assertEquals(
                static::TEST_CC_NUMBER,
                $encyptor->decrypt($payment->getCcNumberEnc())
            );

            $this->assertStringStartsWith('0:' . Encryptor::CIPHER_LATEST . ':', $payment->getCcNumberEnc());
        }
    }
}
