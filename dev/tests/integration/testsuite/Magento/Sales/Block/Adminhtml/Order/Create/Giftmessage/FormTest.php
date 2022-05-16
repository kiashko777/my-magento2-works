<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create\Giftmessage;

use Magento\Backend\Model\Session\Quote;
use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     */
    public function testGetDefaultSenderWithCurrentCustomer()
    {
        /** Preconditions */
        $objectManager = Bootstrap::getObjectManager();
        $fixtureCustomerId = 1;
        /** @var Quote $backendQuoteSession */
        $backendQuoteSession = $objectManager->get(Quote::class);
        $backendQuoteSession->setCustomerId($fixtureCustomerId);
        /** @var Form $block */
        $block = $objectManager->create(Form::class);
        $block->setEntity(new DataObject());

        /** SUT execution and assertions */
        $this->assertEquals('John Smith', $block->getDefaultSender(), 'Sender name is invalid.');
    }
}
