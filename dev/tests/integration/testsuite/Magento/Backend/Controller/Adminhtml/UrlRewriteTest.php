<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Controller\Adminhtml;

use Magento\Cms\Model\Page;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class UrlRewriteTest extends AbstractBackendController
{
    /**
     * Check save cms page rewrite
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testSaveActionCmsPage()
    {
        $page = Bootstrap::getObjectManager()->get(Page::class);
        $page->load('page_design_blank', 'identifier');

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue(
            [
                'description' => 'Some URL rewrite description',
                'options' => 'R',
                'request_path' => 'some_new_path',
                'store_id' => 1,
                'cms_page' => $page->getId(),
            ]
        );
        $this->dispatch('backend/admin/url_rewrite/save');

        $this->assertSessionMessages(
            $this->containsEqual('The URL Rewrite has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/admin/url_rewrite/index'));
    }
}
