<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Review\Block\Adminhtml;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppArea Adminhtml
     */
    public function testConstruct()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var AccountManagementInterface $accountManagement */
        $accountManagement = $objectManager->create(AccountManagementInterface::class);

        /** @var View $customerViewHelper */
        $customerViewHelper = $objectManager->create(View::class);

        $customer = $accountManagement->authenticate('customer@example.com', 'password');
        $request = $objectManager->get(RequestInterface::class);
        $request->setParam('customerId', $customer->getId());
        /** @var LayoutInterface $layout */
        $layout = $objectManager->get(LayoutInterface::class);
        $block = $layout->createBlock(Main::class);
        $customerName = $customerViewHelper->getCustomerName($customer);
        /** @var Escaper $escaper */
        $escaper = Bootstrap::getObjectManager()
            ->get(Escaper::class);
        $this->assertStringMatchesFormat(
            '%A' . __('All Reviews of Customer `%1`', $escaper->escapeHtml($customerName)) . '%A',
            $block->getHeaderHtml()
        );
    }
}
