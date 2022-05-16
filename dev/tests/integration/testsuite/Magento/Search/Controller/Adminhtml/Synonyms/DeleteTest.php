<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Search\Controller\Adminhtml\Synonyms;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Search\Model\ResourceModel\SynonymGroup\Collection;
use Magento\Search\Model\SynonymGroup;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Test for class \Magento\Search\Controller\Adminhtml\Synonyms\Delete
 *
 * @magentoAppArea Adminhtml
 */
class DeleteTest extends AbstractBackendController
{

    /** Test Delete Synonyms
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Search/_files/synonym_group.php
     * @return void
     */
    public function testExecute(): void
    {
        $synonymGroupModel = $this->getTestFixture();
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue(['group_id' => $synonymGroupModel->getGroupId()]);
        $this->dispatch('backend/search/synonyms/delete');
        $this->assertSessionMessages($this->equalTo([(string)__('The synonym group has been deleted.')]));
    }

    /**
     * Gets synonym group Fixture.
     *
     * @return SynonymGroup
     */
    private function getTestFixture(): SynonymGroup
    {
        /** @var Collection */
        $synonymGroupCollection = Bootstrap::getObjectManager()->get(Collection::class);
        return $synonymGroupCollection->getLastItem();
    }

    /**
     * Test execute with no params
     *
     * @return void
     */
    public function testExecuteNoId(): void
    {
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch('backend/search/synonyms/delete');
        $this->assertSessionMessages($this->equalTo([(string)__('We can&#039;t find a synonym group to delete.')]));
    }
}
