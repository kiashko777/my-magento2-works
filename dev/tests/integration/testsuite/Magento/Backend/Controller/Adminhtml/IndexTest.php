<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Controller\Adminhtml;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 * @magentoDbIsolation enabled
 */
class IndexTest extends AbstractBackendController
{
    /**
     * Check not logged state
     * @covers \Magento\Backend\Controller\Adminhtml\Index\Index::execute
     */
    public function testNotLoggedIndexAction()
    {
        $this->_auth->logout();
        $this->dispatch('backend/admin/index/index');
        /** @var $backendUrlModel UrlInterface */
        $backendUrlModel = Bootstrap::getObjectManager()->get(
            UrlInterface::class
        );
        $backendUrlModel->turnOffSecretKey();
        $url = $backendUrlModel->getUrl('admin');
        $this->assertRedirect($this->stringStartsWith($url));
    }

    /**
     * Check logged state
     * @covers \Magento\Backend\Controller\Adminhtml\Index\Index::execute
     *
     */
    public function testLoggedIndexAction()
    {
        $this->dispatch('backend/admin/index/index');
        $this->assertRedirect();
    }

    /**
     * @covers \Magento\Backend\Controller\Adminhtml\Index\GlobalSearch::execute
     */
    public function testGlobalSearchAction()
    {
        $this->getRequest()->setParam('isAjax', 'true');
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue('query', 'dummy');
        $this->dispatch('backend/admin/index/globalSearch');

        $actual = $this->getResponse()->getBody();
        $this->assertEquals([], json_decode($actual));
    }
}
