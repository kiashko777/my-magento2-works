<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Newsletter\Controller\Adminhtml;

use Magento\Backend\Model\Session;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class NewsletterQueueTest extends AbstractBackendController
{
    /**
     * @var \Magento\Newsletter\Model\Template
     */
    protected $_model;

    /**
     * @magentoDataFixture Magento/Newsletter/_files/newsletter_sample.php
     */
    public function testSaveActionQueueTemplateAndVerifySuccessMessage()
    {
        $postForQueue = [
            'sender_email' => 'johndoe_gieee@unknown-domain.com',
            'sender_name' => 'john doe',
            'subject' => 'test subject',
            'text' => 'newsletter text',
        ];
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue($postForQueue);

        // Loading by code, since ID will vary. template_code is not actually used to load anywhere else.
        $this->_model->load('some_unique_code', 'template_code');

        // Ensure that template is actually loaded so as to prevent a false positive on saving a *new* template
        // instead of existing one.
        $this->assertEquals('some_unique_code', $this->_model->getTemplateCode());

        $this->getRequest()->setParam('template_id', $this->_model->getId());
        $this->dispatch('backend/newsletter/queue/save');

        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(['You saved the newsletter queue.']),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->_model = Bootstrap::getObjectManager()->create(
            \Magento\Newsletter\Model\Template::class
        );
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        /**
         * Unset messages
         */
        Bootstrap::getObjectManager()->get(
            Session::class
        )->getMessages(
            true
        );
        $this->_model = null;
    }
}
