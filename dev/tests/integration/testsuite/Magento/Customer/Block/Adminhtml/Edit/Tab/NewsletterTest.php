<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\App\State;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Test Customer account form block functionality
 *
 * @magentoAppArea Adminhtml
 */
class NewsletterTest extends AbstractBackendController
{
    /**
     * @var Newsletter
     */
    private $block;

    /**
     * Core registry
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     */
    public function testRenderingNewsletterBlock()
    {
        $websiteId = 1;
        $this->getRequest()->setParam('id', 1);
        $this->dispatch('backend/customer/index/edit');
        $body = $this->getResponse()->getBody();

        $this->assertStringContainsString('\u003Cspan\u003ENewsletter Information\u003C\/span\u003E', $body);
        $this->assertStringContainsString(
            '\u003Cinput id=\"_newslettersubscription_status_' . $websiteId . '\"',
            $body
        );
        $this->assertStringNotContainsString('checked="checked"', $body);
        $this->assertStringContainsString('\u003Cspan\u003ESubscribed to Newsletter\u003C\/span\u003E', $body);
        $this->assertStringContainsString('\u003ENo Newsletter Found\u003C', $body);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     * @magentoDataFixture Magento/Newsletter/_files/newsletter_sample.php
     * @magentoDataFixture Magento/Newsletter/_files/queue.php
     */
    public function testRenderingNewsletterBlockWithQueue()
    {
        $this->getRequest()->setParam('id', 1);
        $this->dispatch('backend/customer/index/edit');
        $body = $this->getResponse()->getBody();

        $this->assertMatchesRegularExpression(
            '~.+\/newsletter\\\/template\\\/preview\\\/id\\\/\d+\\\/subscriber\\\/\d+\\\/.+~',
            $body
        );
    }

    /**
     * Execute per test initialization.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(State::class)->setAreaCode('Adminhtml');

        $this->coreRegistry = $objectManager->get(Registry::class);
        $this->block = $objectManager->get(
            LayoutInterface::class
        )->createBlock(
            Newsletter::class,
            '',
            ['registry' => $this->coreRegistry]
        )->setTemplate(
            'tab/newsletter.phtml'
        );
    }

    /**
     * Execute post test cleanup
     */
    protected function tearDown(): void
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
}
